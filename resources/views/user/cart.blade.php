@extends('layouts.user_app')

@section('content')

<style>
    .cart-page { max-width: 1100px; }
    .cart-title { font-weight: 800; color: #2b2b2b; }
    .cart-count { color: #8a8a8a; font-size: .95rem; }

    .cart-item {
        display: flex; gap: 1rem; align-items: center;
        background: #fff; border-radius: 18px; padding: 1rem 1.1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
        transition: box-shadow .15s ease;
    }
    .cart-item:hover { box-shadow: 0 8px 18px rgba(0, 0, 0, .11); }
    .cart-item + .cart-item { margin-top: 1rem; }

    .cart-img { width: 96px; height: 96px; flex-shrink: 0; border-radius: 14px; object-fit: cover; background: #eee; }
    .cart-info { flex: 1 1 auto; min-width: 0; }
    .cart-cat { font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: #8a8a8a; }
    .cart-name { font-size: 1.1rem; font-weight: 700; color: #2b2b2b; margin: .1rem 0; }
    .cart-unit { color: #6b6b6b; font-size: .9rem; }
    .cart-warn { color: #c0392b; font-size: .8rem; margin-top: .2rem; }

    .cart-side { text-align: right; flex-shrink: 0; }
    .cart-subtotal { font-weight: 800; font-size: 1.15rem; color: #843f3f; margin-bottom: .5rem; }

    .qty-box {
        display: inline-flex; align-items: center;
        border: 2px solid #e6d3d3; border-radius: 999px; overflow: hidden; background: #fff;
    }
    .qty-btn {
        width: 34px; height: 34px; border: none; background: transparent;
        font-size: 1.1rem; font-weight: 700; color: #843f3f; cursor: pointer;
    }
    .qty-btn:hover { background: #f8eeee; }
    .qty-input {
        width: 44px; height: 34px; border: none; text-align: center; font-weight: 700;
        -moz-appearance: textfield; background: transparent;
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .qty-input:focus { outline: none; }

    .cart-remove {
        border: none; background: none; color: #b0b0b0; font-size: .85rem;
        margin-top: .4rem; cursor: pointer; padding: 0;
    }
    .cart-remove:hover { color: #c0392b; text-decoration: underline; }

    .summary-card {
        background: #fff; border-radius: 20px; padding: 1.4rem;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .09);
        position: sticky; top: 1rem;
    }
    .summary-card h2 { font-size: 1.25rem; font-weight: 800; margin-bottom: 1rem; color: #2b2b2b; }
    .summary-row { display: flex; justify-content: space-between; color: #6b6b6b; margin-bottom: .5rem; }
    .summary-total {
        display: flex; justify-content: space-between; align-items: center;
        border-top: 1px dashed #ddd; padding-top: 1rem; margin-top: 1rem;
        font-size: 1.35rem; font-weight: 800; color: #2b2b2b;
    }
    .summary-total span:last-child { color: #843f3f; }

    .btn-checkout {
        width: 100%; margin-top: 1.2rem; padding: .8rem 1rem; border: none; border-radius: 999px;
        background: #843f3f; color: #fff; font-weight: 700; font-size: 1rem; cursor: pointer;
        transition: background .15s;
    }
    .btn-checkout:hover { background: #6a3232; }
    .btn-checkout:disabled { background: #b5b5b5; cursor: not-allowed; }
    .btn-continue { display: block; text-align: center; margin-top: .8rem; color: #843f3f; font-weight: 600; text-decoration: none; }
    .btn-continue:hover { text-decoration: underline; color: #6a3232; }

    .cart-empty {
        text-align: center; background: #fff; border-radius: 20px;
        padding: 3.5rem 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
    }
    .cart-empty svg { color: #d9b8b8; margin-bottom: 1rem; }
    .cart-empty p { color: #6b6b6b; font-size: 1.15rem; margin-bottom: 1.2rem; }
    .btn-shop {
        display: inline-block; padding: .7rem 1.8rem; border-radius: 999px;
        background: #843f3f; color: #fff; font-weight: 600; text-decoration: none;
    }
    .btn-shop:hover { background: #6a3232; color: #fff; }

    @media (max-width: 575.98px) {
        .cart-item { flex-wrap: wrap; }
        .cart-side { width: 100%; text-align: left; display: flex; align-items: center; justify-content: space-between; }
        .cart-subtotal { margin-bottom: 0; }
    }
</style>

<div class="container cart-page py-4">

    <div class="d-flex align-items-baseline justify-content-between mb-4">
        <h1 class="cart-title mb-0">Your Cart</h1>
        @if (count($cartItems) > 0)
            <span class="cart-count">{{ collect($cartItems)->sum('quantity') }} item(s)</span>
        @endif
    </div>

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
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (count($cartItems) > 0)
        @php
            $hasStockProblem = collect($cartItems)->contains(fn ($i) => $i->product->stock < $i->quantity);
        @endphp

        <div class="row g-4">

            {{-- Items --}}
            <div class="col-lg-8">
                @foreach ($cartItems as $item)
                    @php
                        $p = $item->product;
                        $img = $p->image
                            ? asset('storage/' . $p->image)
                            : 'data:image/svg+xml;utf8,' . rawurlencode(
                                '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
                                . '<rect width="100%" height="100%" fill="#e9e9e9"/>'
                                . '<text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="20" '
                                . 'text-anchor="middle" dominant-baseline="middle">No Image</text></svg>'
                            );
                    @endphp

                    <div class="cart-item">
                        <img class="cart-img" src="{{ $img }}" alt="{{ $p->name }}">

                        <div class="cart-info">
                            <div class="cart-cat">{{ $p->category->name ?? '' }}</div>
                            <div class="cart-name">{{ $p->name }}</div>
                            <div class="cart-unit">${{ number_format($p->price, 2) }} each</div>
                            @if ($p->stock < $item->quantity)
                                <div class="cart-warn">
                                    {{ $p->stock > 0 ? 'Only ' . $p->stock . ' left in stock.' : 'Out of stock.' }}
                                    Please update the quantity.
                                </div>
                            @endif
                        </div>

                        <div class="cart-side">
                            <div class="cart-subtotal">${{ number_format($item->subtotal, 2) }}</div>

                            <form method="POST" action="{{ route('cart.update', $p) }}" class="qty-form">
                                @csrf
                                @method('PATCH')
                                <div class="qty-box">
                                    <button type="button" class="qty-btn" data-step="-1" aria-label="Decrease">&minus;</button>
                                    <input type="number" name="quantity" class="qty-input"
                                           value="{{ $item->quantity }}" min="1" max="{{ max($p->stock, 1) }}">
                                    <button type="button" class="qty-btn" data-step="1" aria-label="Increase">+</button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('cart.remove', $p) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cart-remove">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="col-lg-4">
                <div class="summary-card">
                    <h2>Order Summary</h2>

                    <div class="summary-row">
                        <span>Items ({{ collect($cartItems)->sum('quantity') }})</span>
                        <span>${{ number_format($cartTotal, 2) }}</span>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span>${{ number_format($cartTotal, 2) }}</span>
                    </div>

                    <form method="POST" action="{{ route('order.place') }}">
                        @csrf
                        <button type="submit" class="btn-checkout" @disabled($hasStockProblem)>
                            Proceed to Checkout
                        </button>
                    </form>

                    <a href="{{ route('dashboard.user-dashboard') }}" class="btn-continue">&larr; Continue shopping</a>
                </div>
            </div>
        </div>
    @else
        <div class="cart-empty">
            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <p>Your cart is empty.</p>
            <a href="{{ route('dashboard.user-dashboard') }}" class="btn-shop">Start Shopping</a>
        </div>
    @endif
</div>

<script>
    // +/- buttons change the quantity and submit the form; typing a number also submits on change
    document.querySelectorAll('.qty-form').forEach(function (form) {
        var input = form.querySelector('.qty-input');
        form.querySelectorAll('.qty-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var next = parseInt(input.value, 10) + parseInt(btn.dataset.step, 10);
                var min = parseInt(input.min, 10) || 1;
                var max = parseInt(input.max, 10) || next;
                if (next < min || next > max) return;
                input.value = next;
                form.submit();
            });
        });
        input.addEventListener('change', function () { form.submit(); });
    });
</script>

@endsection