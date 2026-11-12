@extends('layouts.user_app')

@section('content')

<style>
    .od-page { max-width: 900px; }
    .od-title { font-weight: 800; color: #2b2b2b; }
    .od-date { color: #8a8a8a; }

    .od-card {
        background: #fff; border-radius: 20px; padding: 1.4rem;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .09);
    }
    .od-box { background: #faf6f6; border-radius: 14px; padding: 1rem 1.1rem; height: 100%; }
    .od-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #8a8a8a; }
    .od-value { font-size: 1.35rem; font-weight: 800; color: #2b2b2b; margin-top: .15rem; }
    .od-small { color: #6b6b6b; font-size: .95rem; margin-top: .25rem; }

    .od-badge {
        display: inline-block; padding: .3rem .9rem; border-radius: 999px;
        font-weight: 700; font-size: .95rem; margin-top: .25rem;
    }
    .od-pending    { background: #fff4d6; color: #9a6b00; }
    .od-processing { background: #dceeff; color: #1c5d99; }
    .od-shipped    { background: #e6e3ff; color: #4a3fb0; }
    .od-delivered  { background: #daf5e1; color: #1f7a3a; }
    .od-cancelled  { background: #fde0e0; color: #b02a2a; }
    .od-default    { background: #eee; color: #555; }

    .od-section { font-size: 1.2rem; font-weight: 800; color: #2b2b2b; margin: 1.6rem 0 .9rem; }

    .od-item {
        display: flex; align-items: center; gap: 1rem;
        padding: .9rem 0; border-bottom: 1px solid #f0f0f0;
    }
    .od-item:last-child { border-bottom: none; }
    .od-img { width: 64px; height: 64px; flex-shrink: 0; border-radius: 12px; object-fit: cover; background: #eee; }
    .od-info { flex: 1 1 auto; min-width: 0; }
    .od-name { font-weight: 700; color: #2b2b2b; }
    .od-unit { color: #8a8a8a; font-size: .88rem; }
    .od-line { text-align: right; flex-shrink: 0; }
    .od-qty { color: #6b6b6b; font-size: .9rem; }
    .od-sub { font-weight: 800; color: #843f3f; font-size: 1.1rem; }

    .od-total {
        display: flex; justify-content: space-between; align-items: center;
        border-top: 1px dashed #ddd; margin-top: .8rem; padding-top: 1rem;
        font-size: 1.3rem; font-weight: 800; color: #2b2b2b;
    }
    .od-total span:last-child { color: #843f3f; }

    .od-actions { display: flex; flex-wrap: wrap; gap: .8rem; justify-content: center; margin-top: 1.6rem; }
    .od-btn {
        display: inline-block; padding: .65rem 1.6rem; border-radius: 999px;
        font-weight: 600; text-decoration: none; transition: background .15s;
    }
    .od-btn-main { background: #843f3f; color: #fff; }
    .od-btn-main:hover { background: #6a3232; color: #fff; }
    .od-btn-light { border: 2px solid #843f3f; color: #843f3f; background: #fff; }
    .od-btn-light:hover { background: #f8eeee; color: #6a3232; }
</style>

@php
    $statusClass = [
        'Pending'    => 'od-pending',
        'Processing' => 'od-processing',
        'Shipped'    => 'od-shipped',
        'Delivered'  => 'od-delivered',
        'Cancelled'  => 'od-cancelled',
    ][$order->status] ?? 'od-default';
@endphp

<div class="container od-page py-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap align-items-baseline justify-content-between mb-3">
        <h1 class="od-title mb-0">Order #{{ $order->id }}</h1>
        <span class="od-date">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</span>
    </div>

    <div class="od-card">

        {{-- Status / total / customer --}}
        <div class="row g-3">
            <div class="col-md-4">
                <div class="od-box">
                    <div class="od-label">Order status</div>
                    <span class="od-badge {{ $statusClass }}">{{ $order->status }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="od-box">
                    <div class="od-label">Total charged</div>
                    <div class="od-value">${{ number_format($order->total_price, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="od-box">
                    <div class="od-label">Customer</div>
                    <div class="od-small fw-semibold">{{ $order->user->name ?? Auth::user()->name }}</div>
                    <div class="od-small">{{ $order->user->email ?? Auth::user()->email }}</div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="od-section">Items ordered</div>

        @foreach ($order->items as $item)
            @php
                $p = $item->product;
                $unit = $item->quantity > 0 ? $item->price / $item->quantity : 0;
                $img = ($p && $p->image)
                    ? asset('storage/' . $p->image)
                    : 'data:image/svg+xml;utf8,' . rawurlencode(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120">'
                        . '<rect width="100%" height="100%" fill="#e9e9e9"/>'
                        . '<text x="50%" y="50%" fill="#999" font-family="sans-serif" font-size="16" '
                        . 'text-anchor="middle" dominant-baseline="middle">No Image</text></svg>'
                    );
            @endphp

            <div class="od-item">
                <img class="od-img" src="{{ $img }}" alt="{{ $p->name ?? 'Product' }}">

                <div class="od-info">
                    <div class="od-name">{{ $p->name ?? 'Product no longer available' }}</div>
                    <div class="od-unit">${{ number_format($unit, 2) }} each</div>
                </div>

                <div class="od-line">
                    <div class="od-qty">Qty: {{ $item->quantity }}</div>
                    <div class="od-sub">${{ number_format($item->price, 2) }}</div>
                </div>
            </div>
        @endforeach

        <div class="od-total">
            <span>Total</span>
            <span>${{ number_format($order->total_price, 2) }}</span>
        </div>
    </div>

    <div class="od-actions">
        <a href="{{ route('order.history') }}" class="od-btn od-btn-light">&larr; Back to Order History</a>
        <a href="{{ route('dashboard.user-dashboard') }}" class="od-btn od-btn-main">Continue Shopping</a>
    </div>
</div>

@endsection