<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15); 
        $categories = Category::orderBy('name')->get(); 

        return view('admin.products.index', ['products' => $products, 'category' => $categories]);
    }

    public function userIndex()
    {
        // Fetches ALL products for a customer-facing display
        $products = Product::with('category')->latest()->get(); 
        $categories = Category::orderBy('name')->get();

        // Returning to a view path that matches the provided Blade snippet context
        return view('dashboard.user-dashboard.user', ['products' => $products, 'categories' => $categories]);
    }
     /**
     * Public-facing menu — browsable by guests and logged-in users alike.
     * Reuses the same customer-facing view as userIndex().
     */
    // public function menu()
    // {
    //     $products = Product::with('category')->latest()->get();
    //     $categories = Category::orderBy('name')->get();
 
    //     return view('dashboard.user-dashboard.user', ['products' => $products, 'categories' => $categories]);
    // }
    //  /**
    //  * Single product detail page, publicly viewable.
    //  * {$product} is auto-resolved via route-model binding.
    //  *
    // public function show(Product $product)
    // {
    //     return view('menu.show', compact('product'));
    // }

    public function store(Request $request)
    {
        // 1. Validation: Image is REQUIRED on store
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0', 
            'category_id' => 'required|integer|exists:categories,id', 
            'image' => 'required|image|max:2048', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // 2. File Upload
            $imagePath = $request->file('image')->store('product_images', 'public');
        }
        
        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'image' => $imagePath,
            'category_id' => $validated['category_id'],
            'stock' => $validated['stock'], 
        ]);

        if($product){
            return redirect()->route('products.index')->with('success', 'Product added successfully! 🎉');
        } else {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            return back()->with('error', 'Failed to add product. Please try again.');
        }
    }

    public function update(Request $request, Product $product) 
    {
        // 3. Validation for Update: Image is NULLABLE
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|max:2048', 
        ]);

        // Update non-image fields
        $product->fill($validated);

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
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
