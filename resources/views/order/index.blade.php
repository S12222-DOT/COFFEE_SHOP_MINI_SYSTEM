@extends('layouts.user_app')

@section('title', 'Perk Up Coffee - Order')

@section('content')
<div class="container-fluid py-4 page-wrapper">

    {{-- Flash messages (e.g. "Your cart is empty" redirected here from checkout) --}}
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

        {{-- Left side: menu (8 of 12 columns on desktop) --}}
        <div class="col-lg-8 menu-col">
            <div class="p-3 menu-container">

                {{-- 1. Header: logo, search, account / orders / favorite / cart icons --}}
                @include('order.partials.header')

                {{-- 2. Category tabs (needs $categories) --}}
                @include('order.partials.category-tabs')

                {{-- 3. Product grid (needs $products) --}}
                @include('order.partials.product-grid')

            </div>
        </div>

        {{-- Right side: order summary (4 of 12 columns on desktop) --}}
        <div class="col-lg-4 order-col">
            @include('order.partials.order-sidebar')
        </div>

    </div>
</div>
@endsection