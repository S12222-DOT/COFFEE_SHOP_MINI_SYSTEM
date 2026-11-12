<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    // =====================================================================
    //  CUSTOMER: CART
    // =====================================================================

    /**
     * Add a product to the session cart.
     * Route: POST /cart/add   Name: cart.add
     * Answers JSON for the "Add to Cart" script (stays on the page, updates the cart icon),
     * or a normal redirect if JavaScript is off.
     */
    public function addItemToCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $cart    = $request->session()->get('cart', []);
        $newQty  = ($cart[$product->id] ?? 0) + $data['quantity'];

        // Never allow more than the stock
        if ($newQty > $product->stock) {
            $msg = "Only {$product->stock} of {$product->name} available.";

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $cart[$product->id] = $newQty;
        $request->session()->put('cart', $cart);

        $msg = "{$product->name} added to cart!";

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => $msg,
                'cart_count' => array_sum($cart),   // total quantity, shown on the cart icon
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Show the cart page.
     * Route: GET /cart   Name: cart.show
     */
    public function showCart(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        // Load every product of the cart in one query (with its category for the page)
        $products = Product::with('category')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $cartItems = [];
        $cartTotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if ($product) {
                $subtotal   = $product->price * $quantity;
                $cartTotal += $subtotal;

                $cartItems[] = (object) [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            } else {
                // The product was deleted: drop it from the cart
                unset($cart[$productId]);
                $request->session()->put('cart', $cart);
            }
        }

        return view('user.cart', compact('cartItems', 'cartTotal'));
    }

    /**
     * Change the quantity of one cart item.
     * Route: PATCH /cart/{product}   Name: cart.update
     */
    public function updateCartItem(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (!isset($cart[$product->id])) {
            return back()->with('error', 'That item is not in your cart.');
        }

        if ($data['quantity'] > $product->stock) {
            return back()->with('error', "Only {$product->stock} of {$product->name} available.");
        }

        $cart[$product->id] = (int) $data['quantity'];
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove one item from the cart.
     * Route: DELETE /cart/{product}   Name: cart.remove
     */
    public function removeCartItem(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('success', $product->name . ' removed from your cart.');
    }

    // =====================================================================
    //  CUSTOMER: CHECKOUT
    // =====================================================================

    /**
     * Turn the cart into an order.
     * Route: POST /checkout   Name: order.place
     */
    public function placeOrder(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')
                ->with('error', 'Your cart is empty. Please add items to place an order.');
        }

        try {
            // All-or-nothing: if anything fails inside, nothing is saved
            $order = DB::transaction(function () use ($cart) {

                // Lock the product rows so two customers can't buy the last item at the same time
                $products = Product::whereIn('id', array_keys($cart))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $orderTotal = 0;

                // 1. Check every item first
                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);

                    if (!$product) {
                        throw new \RuntimeException('A product in your cart no longer exists.');
                    }
                    if ($product->stock < $quantity) {
                        throw new \RuntimeException("Insufficient stock for {$product->name}. Only {$product->stock} remaining.");
                    }

                    $orderTotal += $product->price * $quantity;
                }

                // 2. Create the order
                $order = Order::create([
                    'user_id'     => Auth::id(),
                    'total_price' => $orderTotal,
                    'status'      => 'Pending',
                ]);

                // 3. Create the order items and take the stock
                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);

                    OrderItem::create([
                        'order_id'        => $order->id,
                        'product_id'      => $productId,
                        'quantity'        => $quantity,
                        'price'           => $product->price,
                        'subtotal'        => $product->price * $quantity,
                    ]);

                    $product->decrement('stock', $quantity);
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.show')->with('error', $e->getMessage());
        }

        // 4. Clear the cart only after the order is saved
        $request->session()->forget('cart');

        return redirect()->route('order.details', $order)
            ->with('success', 'Order placed successfully! You will receive a confirmation shortly.');
    }

    // =====================================================================
    //  CUSTOMER: ORDER HISTORY & DETAILS
    // =====================================================================

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.order_history', compact('orders'));
    }

    public function show(Order $order)
    {
        // a customer can only open their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('items.product');

        return view('user.order_details', compact('order'));
    }

    // =====================================================================
    //  ADMIN: ORDER MANAGEMENT
    // =====================================================================

    // List all orders (optional ?status=Pending filter)
    public function adminIndex(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->withCount('items')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.orders.index', compact('orders'));
    }

    // Change the status of an order
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'Completed', 'Cancelled'])],
        ]);

        $newStatus = $data['status'];

        // A cancelled order is final (its stock was already given back)
        if ($order->status === 'Cancelled') {
            return back()->with('error', "Order #{$order->id} is cancelled and can't be changed.");
        }

        DB::transaction(function () use ($order, $newStatus) {

            // Cancelling puts the products back in stock
            if ($newStatus === 'Cancelled') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)
                        ->increment('stock', $item->quantity);
                }
    
            }

            $order->update(['status' => $newStatus]);
        });

        return back()->with('success', "Order #{$order->id} is now {$newStatus}.");
    }
}