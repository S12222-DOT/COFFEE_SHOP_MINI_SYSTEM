@extends('layouts.user_app')

@section('content')

<style>
    .oh-page { max-width: 950px; }
    .oh-title { font-weight: 800; color: #2b2b2b; }
    .oh-count { color: #8a8a8a; font-size: .95rem; }

    .oh-row {
        display: flex; align-items: center; gap: 1rem;
        background: #fff; border-radius: 18px; padding: 1rem 1.2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .oh-row:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(0, 0, 0, .11); }
    .oh-row + .oh-row { margin-top: 1rem; }

    .oh-id { flex: 0 0 110px; }
    .oh-id strong { font-size: 1.1rem; color: #2b2b2b; }
    .oh-date { color: #8a8a8a; font-size: .85rem; }
    .oh-mid { flex: 1 1 auto; min-width: 0; color: #6b6b6b; font-size: .92rem; }
    .oh-total { flex: 0 0 110px; text-align: right; font-weight: 800; font-size: 1.1rem; color: #843f3f; }
    .oh-status { flex: 0 0 120px; text-align: center; }
    .oh-view { flex: 0 0 auto; }

    .oh-badge {
        display: inline-block; padding: .28rem .85rem; border-radius: 999px;
        font-weight: 700; font-size: .8rem;
    }
    .oh-pending    { background: #fff4d6; color: #9a6b00; }
    .oh-processing { background: #dceeff; color: #1c5d99; }
    .oh-shipped    { background: #e6e3ff; color: #4a3fb0; }
    .oh-delivered  { background: #daf5e1; color: #1f7a3a; }
    .oh-cancelled  { background: #fde0e0; color: #b02a2a; }
    .oh-default    { background: #eee; color: #555; }

    .oh-btn {
        display: inline-block; padding: .45rem 1.1rem; border-radius: 999px;
        border: 2px solid #843f3f; color: #843f3f; background: #fff;
        font-weight: 600; font-size: .88rem; text-decoration: none; white-space: nowrap;
        transition: background .15s, color .15s;
    }
    .oh-btn:hover { background: #843f3f; color: #fff; }

    .oh-empty {
        text-align: center; background: #fff; border-radius: 20px;
        padding: 3.5rem 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
    }
    .oh-empty svg { color: #d9b8b8; margin-bottom: 1rem; }
    .oh-empty p { color: #6b6b6b; font-size: 1.15rem; margin-bottom: 1.2rem; }
    .oh-shop {
        display: inline-block; padding: .7rem 1.8rem; border-radius: 999px;
        background: #843f3f; color: #fff; font-weight: 600; text-decoration: none;
    }
    .oh-shop:hover { background: #6a3232; color: #fff; }

    @media (max-width: 767.98px) {
        .oh-row { flex-wrap: wrap; }
        .oh-id, .oh-mid, .oh-total, .oh-status, .oh-view { flex: 1 1 45%; text-align: left; }
        .oh-total { text-align: right; }
        .oh-view { text-align: right; }
    }
</style>

<div class="container oh-page py-4">

    <div class="d-flex align-items-baseline justify-content-between mb-4">
        <h1 class="oh-title mb-0">Your Orders</h1>
        @if (!$orders->isEmpty())
            <span class="oh-count">{{ $orders->total() }} order(s)</span>
        @endif
    </div>

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

    @if ($orders->isEmpty())
        <div class="oh-empty">
            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <p>You have not placed any orders yet.</p>
            <a href="{{ route('user.index') }}" class="oh-shop">Start Shopping</a>
        </div>
    @else
        @foreach ($orders as $order)
            @php
                // the database may store "pending" or "Pending": compare in lowercase
                $statusClass = [
                    'pending'    => 'oh-pending',
                    'processing' => 'oh-processing',
                    'shipped'    => 'oh-shipped',
                    'delivered'  => 'oh-delivered',
                    'completed'  => 'oh-delivered',
                    'cancelled'  => 'oh-cancelled',
                ][strtolower($order->status)] ?? 'oh-default';
            @endphp

            <div class="oh-row">
                <div class="oh-id">
                    <strong>#{{ $order->id }}</strong>
                    <div class="oh-date">{{ $order->created_at->format('M d, Y') }}</div>
                </div>

                <div class="oh-mid">
                    @isset($order->items_count)
                        {{ $order->items_count }} item(s)
                    @else
                        {{ $order->created_at->diffForHumans() }}
                    @endisset
                </div>

                <div class="oh-total">${{ number_format($order->total_price, 2) }}</div>

                <div class="oh-status">
                    <span class="oh-badge {{ $statusClass }}">{{ ucfirst(strtolower($order->status)) }}</span>
                </div>

                <div class="oh-view">
                    <a href="{{ route('order.details', $order) }}" class="oh-btn">View Details</a>
                </div>
            </div>
        @endforeach

        <div class="mt-4 d-flex justify-content-center">
            {{-- bootstrap-5 view: fixes the huge arrows (the default view is made for Tailwind) --}}
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection 