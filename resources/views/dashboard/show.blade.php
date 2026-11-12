@extends('layouts.user_app')

@section('title', $product->name . ' - Perk Up Coffee')

@section('content')

@php
    $soldOut = $product->stock <= 0;

    // Gray "No Image" block if the product has no picture (works offline)
    $noImage = fn ($w, $h, $size) => 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h . '">'
        . '<rect width="100%" height="100%" fill="#e9e9e9"/>'
        . '<text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="' . $size . '" '
        . 'text-anchor="middle" dominant-baseline="middle">No Image</text></svg>'
    );

    $imageUrl = $product->image ? asset('storage/' . $product->image) : $noImage(800, 600, 36);
@endphp

<style>
    .pd-page { max-width: 1000px; }
    .pd-back { color: #843f3f; font-weight: 600; text-decoration: none; }
    .pd-back:hover { text-decoration: underline; color: #6a3232; }

    .pd-card { background: #fff; border-radius: 22px; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,.09); }
    .pd-img { width: 100%; height: 100%; min-height: 300px; max-height: 480px; object-fit: cover; display: block; }
    .pd-body { padding: 2rem; }
    .pd-cat { font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; color: #8a8a8a; text-decoration: none; }
    .pd-name { font-size: 2rem; font-weight: 800; color: #2b2b2b; margin: .2rem 0 .5rem; }
    .pd-price { font-size: 1.8rem; font-weight: 800; color: #843f3f; }
    .pd-desc { color: #555; margin: 1rem 0; line-height: 1.6; }
    .pd-stock { font-size: .92rem; font-weight: 600; margin-bottom: 1.2rem; }
    .pd-in { color: #1f7a3a; } .pd-low { color: #b8860b; } .pd-out { color: #c0392b; }

    .pd-actions { display: flex; align-items: center; gap: .8rem; flex-wrap: wrap; }
    .qty-box { display: inline-flex; align-items: center; border: 2px solid #e6d3d3; border-radius: 999px; overflow: hidden; background: #fff; }
    .qty-btn { width: 40px; height: 40px; border: none; background: transparent; font-size: 1.2rem; font-weight: 700; color: #843f3f; cursor: pointer; }
    .qty-btn:hover { background: #f8eeee; }
    .qty-input { width: 52px; height: 40px; border: none; text-align: center; font-weight: 700; background: transparent; -moz-appearance: textfield; }
    .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .qty-input:focus { outline: none; }
    .pd-add { flex: 1 1 auto; min-width: 180px; padding: .75rem 1.5rem; border: none; border-radius: 999px; background: #843f3f; color: #fff; font-weight: 700; cursor: pointer; transition: background .15s; }
    .pd-add:hover { background: #6a3232; }
    .pd-add:disabled { background: #b5b5b5; cursor: not-allowed; }

    .pd-rel-title { font-size: 1.3rem; font-weight: 800; color: #2b2b2b; margin: 2.2rem 0 1rem; }
    .pd-rel { display: block; background: #fff; border-radius: 16px; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 4px 12px rgba(0,0,0,.07); transition: transform .15s; height: 100%; }
    .pd-rel:hover { transform: translateY(-3px); color: inherit; }
    .pd-rel img { width: 100%; height: 120px; object-fit: cover; display: block; background: #eee; }
    .pd-rel-body { padding: .7rem .9rem; }
    .pd-rel-name { font-weight: 700; font-size: .95rem; }
    .pd-rel-price { color: #843f3f; font-weight: 800; }
</style>

<div class="container pd-page py-4">

    {{-- Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
    @endif

    {{-- Back link: returns to the product list, same category --}}
    <a href="{{ route('dashboard.user-dashboard', array_filter(['category' => $product->category_id])) }}" class="pd-back">
        &larr; Back to {{ $product->category->name ?? 'menu' }}
    </a>

    {{-- Product --}}
    <div class="pd-card mt-3">
        <div class="row g-0">
            <div class="col-md-6">
                <img class="pd-img" src="{{ $imageUrl }}" alt="{{ $product->name }}">
            </div>

            <div class="col-md-6">
                <div class="pd-body">
                    @if ($product->category)
                        <a class="pd-cat" href="{{ route('home', ['category' => $product->category_id]) }}">
                            {{ $product->category->name }}
                        </a>
                    @endif

                    <h1 class="pd-name">{{ $product->name }}</h1>
                    <div class="pd-price">${{ number_format($product->price, 2) }}</div>

                    @if (!empty($product->description))
                        <p class="pd-desc">{{ $product->description }}</p>
                    @endif

                    {{-- Stock --}}
                    @if ($soldOut)
                        <div class="pd-stock pd-out mt-3">Out of stock</div>
                    @elseif ($product->stock <= 5)
                        <div class="pd-stock pd-low mt-3">Only {{ $product->stock }} left</div>
                    @else
                        <div class="pd-stock pd-in mt-3">In stock</div>
                    @endif

                    {{-- Add to cart --}}
                    <form action="{{ route('cart.add') }}" method="POST" class="pd-actions">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="qty-box">
                            <button type="button" class="qty-btn" data-step="-1" aria-label="Decrease" @disabled($soldOut)>&minus;</button>
                            <input type="number" name="quantity" id="qty" class="qty-input"
                                   value="1" min="1" max="{{ max($product->stock, 1) }}" @disabled($soldOut)>
                            <button type="button" class="qty-btn" data-step="1" aria-label="Increase" @disabled($soldOut)>+</button>
                        </div>

                        <button type="submit" class="pd-add" @disabled($soldOut)>
                            {{ $soldOut ? 'Sold Out' : 'Add to Cart' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- More from the same category --}}
    @if ($related->isNotEmpty())
        <div class="pd-rel-title">More from {{ $product->category->name }}</div>
        <div class="row g-3">
            @foreach ($related as $item)
                <div class="col-6 col-md-3">
                    <a href="{{ route('menu.show', $item) }}" class="pd-rel">
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : $noImage(400, 240, 22) }}" alt="{{ $item->name }}">
                        <div class="pd-rel-body">
                            <div class="pd-rel-name">{{ $item->name }}</div>
                            <div class="pd-rel-price">${{ number_format($item->price, 2) }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    // + / - buttons change the quantity inside the allowed range
    (function () {
        var input = document.getElementById('qty');
        if (!input) return;
        document.querySelectorAll('.qty-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var next = (parseInt(input.value, 10) || 1) + parseInt(btn.dataset.step, 10);
                var min = parseInt(input.min, 10) || 1;
                var max = parseInt(input.max, 10) || next;
                if (next >= min && next <= max) input.value = next;
            });
        });
    })();
</script>

@endsection