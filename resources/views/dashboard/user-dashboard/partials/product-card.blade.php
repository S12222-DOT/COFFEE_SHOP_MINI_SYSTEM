{{-- One product card. No outer column wrapper: the grid in product-grid.blade.php sets the width. --}}
@php
    $imageUrl = $product->image
        ? asset('storage/' . $product->image)
        : 'data:image/svg+xml;utf8,' . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400">'
            . '<rect width="100%" height="100%" fill="#e9e9e9"/>'
            . '<text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="28" '
            . 'text-anchor="middle" dominant-baseline="middle">No Image</text></svg>'
        );
    $soldOut = $product->stock <= 0;
@endphp

{{-- @once makes this <style> render a single time per page, not once per card --}}
@once
<style>
    .pc-card {
        display: flex; flex-direction: column; height: 100%;
        background: #fff; border-radius: 20px; overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .pc-card:hover { transform: translateY(-3px); box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12); }
    .pc-img { width: 100%; height: 190px; object-fit: cover; display: block; }
    .pc-body { padding: 1rem 1.1rem 0.5rem; flex-grow: 1; }
    .pc-cat { font-size: .75rem; color: #8a8a8a; text-transform: uppercase; letter-spacing: .04em; }
    .pc-name { font-size: 1.1rem; font-weight: 700; margin: .15rem 0 .25rem; color: #2b2b2b; line-height: 1.25; }
    .pc-price { font-weight: 800; font-size: 1.1rem; color: #843f3f; }
    .pc-stock { font-size: .8rem; color: #c0392b; margin-top: .25rem; }
    .pc-actions {
        display: flex; align-items: center; justify-content: space-between; gap: .6rem;
        padding: .8rem 1.1rem 1rem; border-top: 1px solid #f0f0f0;
    }
    .pc-fav {
        width: 42px; height: 42px; flex-shrink: 0; border-radius: 50%;
        border: 2px solid #e57373; background: #fff; color: #e57373;
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
        transition: background .15s;
    }
    .pc-fav:hover { background: #fdecec; }
    .pc-add-form { flex-grow: 1; margin: 0; }
    .pc-add {
        width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: .35rem;
        padding: .6rem 1rem; border: none; border-radius: 999px; cursor: pointer;
        background: #843f3f; color: #fff; font-weight: 600; font-size: .9rem;
        transition: background .15s;
    }
    .pc-add:hover { background: #6a3232; }
    .pc-add:disabled { background: #b5b5b5; cursor: not-allowed; }
</style>
@endonce

<div class="pc-card" data-category="{{ $product->category_id }}">
    <a href="{{ route('menu.show', $product) }}">
        <img class="pc-img" src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
    </a>

    <div class="pc-body">
        <div class="pc-cat">{{ $product->category->name ?? '' }}</div>
        <div class="pc-name">{{ $product->name }}</div>
        <div class="pc-price">${{ number_format($product->price, 2) }}</div>
        @if ($soldOut)
            <div class="pc-stock">Out of stock</div>
        @endif
    </div>

    <div class="pc-actions">
        {{-- heart: filled when this product is in the customer's favorites --}}
        @php $isFav = in_array($product->id, $favoriteIds ?? []); @endphp
        <button type="button" class="pc-fav favorite-btn {{ $isFav ? 'active' : '' }}"
                data-url="{{ route('favorites.toggle', $product) }}" aria-label="Add to favorites">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </button>

        {{-- data-ajax-cart: cart-ajax.blade.php adds it without leaving the page --}}
        <form action="{{ route('cart.add') }}" method="POST" class="pc-add-form" data-ajax-cart>
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">

            <button type="submit" class="pc-add" @disabled($soldOut)>
                @if ($soldOut)
                    Sold Out
                @else
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add to Cart
                @endif
            </button>
        </form>
    </div>
</div>