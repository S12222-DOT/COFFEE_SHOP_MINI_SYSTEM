@extends('layouts.user_app')

{{-- @section('title', 'My Favorites - Perk Up Coffee')

@section('content')

@include('dashboard.user-dashboard.partials.header') --}}

<style>
    .fv-page { max-width: 950px; }
    .fv-title { font-weight: 800; color: #2b2b2b; }
    .fv-count { color: #8a8a8a; font-size: .95rem; }

    .fv-row {
        display: flex; align-items: center; gap: 1rem;
        background: #fff; border-radius: 18px; padding: .9rem 1.2rem;http://127.0.0.1:8000
        box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .fv-row:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(0, 0, 0, .11); }
    .fv-row + .fv-row { margin-top: 1rem; }

    .fv-img { width: 72px; height: 72px; border-radius: 14px; object-fit: cover; flex: 0 0 72px; background: #eee; }
    .fv-info { flex: 1 1 auto; min-width: 0; }
    .fv-cat { font-size: .75rem; color: #8a8a8a; text-transform: uppercase; letter-spacing: .04em; }
    .fv-name { font-weight: 700; font-size: 1.05rem; color: #2b2b2b; text-decoration: none; display: block; }
    .fv-name:hover { color: #843f3f; }
    .fv-stock { font-size: .8rem; color: #c0392b; }
    .fv-price { flex: 0 0 100px; text-align: right; font-weight: 800; font-size: 1.1rem; color: #843f3f; }
    .fv-actions { flex: 0 0 auto; display: flex; align-items: center; gap: .6rem; }
    .fv-actions form { margin: 0; }

    .fv-btn {
        display: inline-block; padding: .45rem 1.1rem; border-radius: 999px;
        border: 2px solid #843f3f; color: #fff; background: #843f3f;
        font-weight: 600; font-size: .88rem; white-space: nowrap; cursor: pointer;
        transition: background .15s, color .15s;
    }
    .fv-btn:hover { background: #6a3232; border-color: #6a3232; }
    .fv-btn:disabled { background: #b5b5b5; border-color: #b5b5b5; cursor: not-allowed; }

    .fv-remove {
        width: 40px; height: 40px; border-radius: 50%; cursor: pointer;
        border: 2px solid #e57373; background: #fff; color: #e57373;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background .15s, color .15s;
    }
    .fv-remove:hover { background: #e57373; color: #fff; }

    .fv-empty {
        text-align: center; background: #fff; border-radius: 20px;
        padding: 3.5rem 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
    }
    .fv-empty svg { color: #d9b8b8; margin-bottom: 1rem; }
    .fv-empty p { color: #6b6b6b; font-size: 1.15rem; margin-bottom: 1.2rem; }
    .fv-shop {
        display: inline-block; padding: .7rem 1.8rem; border-radius: 999px;
        background: #843f3f; color: #fff; font-weight: 600; text-decoration: none;
    }
    .fv-shop:hover { background: #6a3232; color: #fff; }

    @media (max-width: 767.98px) {
        .fv-row { flex-wrap: wrap; }
        .fv-info { flex: 1 1 60%; }
        .fv-price { flex: 1 1 30%; }
        .fv-actions { flex: 1 1 100%; justify-content: space-between; }
    }
</style>

<div class="container fv-page py-4">

    <div class="d-flex align-items-baseline justify-content-between mb-4">
        <h1 class="fv-title mb-0">My Favorites</h1>
        @if ($products->total())
            <span class="fv-count">{{ $products->total() }} item(s)</span>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($products->isEmpty())
        <div class="fv-empty">
            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <p>You have no favorites yet.<br>Tap the heart on a product to save it here.</p>
            <a href="{{ route('dashboard.user-dashboard') }}" class="fv-shop">Browse the Menu</a>
        </div>
    @else
        @foreach ($products as $product)
            @php
                $imageUrl = $product->image
                    ? asset('storage/' . $product->image)
                    : 'data:image/svg+xml;utf8,' . rawurlencode(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="144" height="144"><rect width="100%" height="100%" fill="#e9e9e9"/></svg>'
                    );
                $soldOut = $product->stock <= 0;
            @endphp

            <div class="fv-row">
                <a href="{{ route('menu.show', $product) }}">
                    <img class="fv-img" src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                </a>

                <div class="fv-info">
                    <div class="fv-cat">{{ $product->category->name ?? '' }}</div>
                    <a href="{{ route('menu.show', $product) }}" class="fv-name">{{ $product->name }}</a>
                    @if ($soldOut)
                        <div class="fv-stock">Out of stock</div>
                    @endif
                </div>

                <div class="fv-price">${{ number_format($product->price, 2) }}</div>

                <div class="fv-actions">
                    {{-- data-ajax-cart: cart-ajax adds it and updates the cart icon without leaving the page --}}
                    <form action="{{ route('cart.add') }}" method="POST" data-ajax-cart>
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="fv-btn" @disabled($soldOut)>
                            {{ $soldOut ? 'Sold Out' : 'Add to Cart' }}
                        </button>
                    </form>

                    {{-- plain form (no AJAX): toggling removes it, then the page reloads without this row --}}
                    <form action="{{ route('favorites.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="fv-remove" aria-label="Remove from favorites" title="Remove from favorites">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6M14 11v6"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
{{-- 
@include('dashboard.user-dashboard.partials.cart-ajax') --}}

{{-- @endsection --}}