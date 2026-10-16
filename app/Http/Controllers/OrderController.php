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
    // --- 🛒 CUSTOMER CART METHODS ---

    /**
     * Adds an item (product) to the user's session cart.
     * Route: POST /cart/add
     * Name: cart.add
     */
    public function addItemToCart(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = $request->session()->get('cart', []);

        // Add or update the quantity for the product
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.show')->with('success', 'Product added to cart!');
    }
    public function showCart(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        // Get product details for all items in the cart
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        $cartTotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if ($product) {
                $subtotal = $product->price * $quantity;
                $cartTotal += $subtotal;
                $cartItems[] = (object) [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            } else {
                // Remove invalid product from cart
                unset($cart[$productId]);
                $request->session()->put('cart', $cart);
            }
        }

        return view('user.cart', compact('cartItems', 'cartTotal'));
    }

    // --- CUSTOMER CHECKOUT & ORDER PLACEMENT ---
    public function placeOrder(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty. Please add items to place an order.');
        }

        return DB::transaction(function () use ($request, $cart) {
            $productIds = array_keys($cart);
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $orderTotal = 0;

            // 1. Pre-Check: Check stock availability
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product || $product->stock < $quantity) {
                    DB::rollBack();
                    return redirect()->route('cart.show')->with('error', "Insufficient stock for {$product->name}. Only {$product->stock} remaining.");
                }
                $orderTotal += $product->price * $quantity;
            }

            // 2. Create the main Order record
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $orderTotal,
                'status' => 'Pending', 
            ]);

            // 3. Create OrderItem records and update product stock
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity, 
                    'price' => $product->price * $quantity, 
                ]);

                $product->stock -= $quantity;
                $product->save();
            }

            // 4. Clear the cart session
            $request->session()->forget('cart');
            return redirect()->route('order.details', $order)->with('success', 'Order placed successfully! You will receive a confirmation shortly.');
        });
    }

    // --- CUSTOMER ORDER HISTORY & DETAILS ---
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Paginate for large history

        return view('user.order_history', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load the related items and products
        $order->load('items.product');

        return view('user.order_details', compact('order'));
    }

    // --- ADMIN MANAGEMENT  ---
    public function adminIndex()
    {
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

           
            return view('admin.orders.index', compact('orders'));
        // return view('admin.order_management', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in(['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'])],
        ]);

        $order->status = $request->input('status');
        $order->save();

        return back()->with('success', "Order #{$order->id} status updated to {$order->status}.");
    }
}
