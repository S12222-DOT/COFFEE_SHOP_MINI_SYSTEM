{{-- 
<div class="col-6 col-md-4 col-lg-3">
    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">

        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/600x400?text=No+Image' }}"
             alt="{{ $product->name }}"
             class="card-img-top" style="height:200px;object-fit:cover;">

        <div class="card-body">
            <small class="text-muted">{{ $product->category->name ?? '' }}</small>
            <h5 class="card-title mb-1">{{ $product->name }}</h5>
            <div class="fw-bold" style="color:#843f3f;">${{ number_format($product->price, 2) }}</div>
            @if ($product->stock <= 0)
                <small class="text-danger">Out of stock</small>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <button type="button" class="btn btn-outline-danger rounded-circle favorite-btn"
                    style="width:44px;height:44px;" data-product-id="{{ $product->id }}">
                <i class="bi bi-heart"></i>
            </button>

            <form action="{{ route('cart.add') }}" method="POST" class="ms-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                @if ($product->stock > 0)
                    <button type="submit" class="btn rounded-pill px-4 text-white" style="background:#843f3f;">
                        <i class="bi bi-plus-lg me-1"></i> Add to Cart
                    </button>
                @else
                    <button type="button" class="btn btn-secondary rounded-pill px-4" disabled>Sold Out</button>
                @endif
            </form>
        </div>
    </div>
</div> --}}

{{-- One product card. No outer column wrapper: the grid in product-grid.blade.php sets the width. --}}
@php
    $imageUrl = $product->image
        ? asset('storage/' . $product->image)
        : 'https://placehold.co/600x400?text=No+Image';
    $soldOut = $product->stock <= 0;
@endphp

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

<div class="pc-card" data-category="{{ $product->category_id }}">
    <img class="pc-img" src="{{ $imageUrl }}" alt="{{ $product->name }}">

    <div class="pc-body">
        <div class="pc-cat">{{ $product->category->name ?? '' }}</div>
        <div class="pc-name">{{ $product->name }}</div>
        <div class="pc-price">${{ number_format($product->price, 2) }}</div>
        @if ($soldOut)
            <div class="pc-stock">Out of stock</div>
        @endif
    </div>

    <div class="pc-actions">
        <button type="button" class="pc-fav favorite-btn" data-product-id="{{ $product->id }}" aria-label="Add to favorites">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </button>

        <form action="{{ route('cart.add') }}" method="POST" class="pc-add-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">

            <button type="submit" class="pc-add" {{ $soldOut ? 'disabled' : '' }}>
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