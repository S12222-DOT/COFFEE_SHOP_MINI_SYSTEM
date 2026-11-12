@extends('layouts.user_app')

@section('title', 'Perk Up Coffee - Tap In & Order')

@section('content')

{{-- Header: logo, search, account / orders / favorite / cart icons.
     Delete this line if layouts/user_app.blade.php already includes the header (you would see it twice). --}}
@include('dashboard.user-dashboard.partials.header')

<div class="container-fluid py-4">

    {{-- Messages: "Added to cart", "Your cart is empty", stock errors, ... --}}
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

    <div class="row g-4">

        {{-- Left side: the menu (8 of 12 columns on desktop) --}}
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-3 p-md-4">

                {{-- Category tabs (needs $categories) --}}
                @include('dashboard.user-dashboard.partials.category-tabs')

                {{-- Product grid (needs $products) --}}
                <div class="mt-4">
                    @include('dashboard.user-dashboard.partials.product-grid')
                </div>

            </div>
        </div>

        {{-- Right side: order summary, stays in view while scrolling (4 of 12 columns) --}}
        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 position-sticky" style="top: 1rem;">
                @include('dashboard.user-dashboard.partials.order-sidebar')
            </div>
        </div>

    </div>
</div>
{{-- Scripts: Add to Cart without reload + heart / Favorite panel (cart-ajax must come first) --}}
{{-- @include('dashboard.user-dashboard.partials.favorite-ajax') --}}
@include('dashboard.user-dashboard.partials.favorite-ajax')

@endsection