<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // =====================================================================
    //  ADMIN: product list
    // =====================================================================

    public function index()
    {
        $products   = Product::with('category')->latest()->paginate(15);
        $categories = Category::orderBy('name')->get();

        // 'category' is kept because your admin view already uses that name
        return view('admin.products.index', [
            'products'   => $products,
            'category'   => $categories,
            'categories' => $categories,
        ]);
    }

    // =====================================================================
    //  CUSTOMER: menu page (product list + category filter + search)
    // =====================================================================

    /**
     * /  and  /user-dashboard/user
     *   /?category=2        only category 2
     *   /?search=latte      name contains "latte"
     */
    public function userIndex(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($request->category, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(8)
            ->appends($request->query());        // keeps ?category= and ?search= on page 2, 3...

        // ids of the products this customer hearted (empty for guests / if favorites aren't set up)
        $favoriteIds = $this->favoriteIds();

        return view('dashboard.user-dashboard.user', compact('products', 'categories', 'favoriteIds'));
    }

    // Old name: if a route still points to menu(), it shows the same page
    public function menu(Request $request)
    {
        return $this->userIndex($request);
    }

    /**
     * Product detail page: /menu/{product}
     * {product} is found automatically by route-model binding (404 if it doesn't exist).
     */
    public function show(Product $product)
    {
        $product->load('category');

        // other products of the same category, NOT the current one
        $related = Product::where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        $favoriteIds = $this->favoriteIds();

        return view('dashboard.show', compact('product', 'related', 'favoriteIds'));
    }

    private function favoriteIds(): array
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'favorites')) {
            return [];
        }

        return $user->favorites()->pluck('products.id')->all();
    }

    // =====================================================================
    //  ADMIN: create / update / delete
    // =====================================================================

    public function store(Request $request)
    {
        // Image is REQUIRED when creating
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0.01',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'image'       => 'required|image|max:2048',
        ]);

        $imagePath = $request->file('image')->store('product_images', 'public');

        Product::create([
            'name'        => $validated['name'],
            'price'       => $validated['price'],
            'image'       => $imagePath,
            'category_id' => $validated['category_id'],
            'stock'       => $validated['stock'],
        ]);

        return redirect()->route('products.index')->with('success', 'Product added successfully! 🎉');
    }

    public function update(Request $request, Product $product)
    {
        // Image is OPTIONAL when updating
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0.01',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'image'       => 'nullable|image|max:2048',
        ]);

        // fill everything except the image (the file is handled below)
        $product->fill(collect($validated)->except('image')->all());

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->image = $request->file('image')->store('product_images', 'public');
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // keep the order history: a product that was ordered can't be deleted
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return redirect()->route('products.index')
                ->with('error', "{$product->name} has been ordered and cannot be deleted. Set its stock to 0 instead.");
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}