<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteController extends Controller
{
    /**
     * GET /favorites: full page with all the customer's favorite products.
     */
    public function index(Request $request)
{
    $products = $request->user()
        ->favorites()
        ->with('category')
        ->orderBy('favorites.created_at', 'desc')
        ->paginate(8);

    // getCollection() gives the plain list of this page, so the editor knows pluck() exists
    $favoriteIds = $products->pluck('id')->all();
    return view('dashboard.user-dashboard.partials.favorites-index', compact('products', 'favoriteIds'));
}
    /**
     * POST /favorites/{product}/toggle: add if not a favorite yet, remove if it is.
     * Answers with the latest list so the Favorite icon panel can refresh itself.
     */
    public function toggle(Request $request, Product $product)
    {
        $user   = $request->user();
        $result = $user->favorites()->toggle($product->id);   // ['attached' => [...], 'detached' => [...]]

        $favorited = count($result['attached']) > 0;
        $message   = $favorited
            ? "{$product->name} added to favorites."
            : "{$product->name} removed from favorites.";

        if ($request->expectsJson()) {
            return response()->json([
                'success'        => true,
                'favorited'      => $favorited,
                'message'        => $message,
                'toggle_url'     => route('favorites.toggle', $product),
                'favorite_count' => $user->favorites()->count(),
                'favorites'      => $this->panelList($user),
            ]);
        }

        return back()->with('success', $message);   // fallback if JavaScript is off
    }

    /**
     * The newest 8 favorites in the small shape the header panel needs.
     */
    private function panelList( $user): array
    {
        return  $user->favorites()
            ->orderBy('favorites.created_at', 'desc')
            ->take(8)
            ->get()
            ->map(fn (Product $p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'price'      => number_format($p->price, 2),
                'image'      => $p->image ? asset('storage/' . $p->image) : null,
                'url'        => route('menu.show', $p),
                'toggle_url' => route('favorites.toggle', $p),
            ])
            ->values()
            ->all();
    }
}