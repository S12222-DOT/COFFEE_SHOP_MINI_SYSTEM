@extends('layouts.user_app')
{{-- Vibe Check: Less 'Order Now,' more 'What's the Sitch?' --}}
@section('title', 'Perk Up Coffee - Tap In & Order')
@section('content')
<div class="coffee-shop-wrapper min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    {{-- Header is serving the look --}}
    @include('dashboard.user-dashboard.partials.header')
    
    <div class="main-content pt-4 md:pt-6 lg:pt-8">
        {{-- Container: Keeping it tight (max-w-7xl) --}}
        <div class="container-fluid max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
            {{-- Grid Setup: This is where the magic happens (flex/grid gap) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                
                {{-- Left Side: The Menu Drop (Spanning 12 cols on mobile, 8 on desktop) --}}
                <div class="lg:col-span-8 menu-section bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 md:p-6 lg:h-[calc(100vh-100px)] lg:overflow-y-auto custom-scrollbar">
                    {{-- Scrollable on desktop so the order stays put. Big brain move. --}}
                    <div class="menu-container">
                        {{-- Category Tabs: Slaying the navigation game --}}
                        @include('dashboard.user-dashboard.partials.category-tabs') 

                        <div class="product-grid-container mt-6">
                            {{-- Product Grid: Get ready for the FOMO --}}
                            @include('dashboard.user-dashboard.partials.product-grid')
                        </div>
                    </div>
                </div>
                
                {{-- Right Side: The Haul (Order Summary). Sticky on desktop. --}}
                <div class="lg:col-span-4 order-section lg:sticky lg:top-8 bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 md:p-6 h-fit">
                    {{-- Order Sidebar: Where the subtotal pops off --}}
                    @include('dashboard.user-dashboard.partials.order-sidebar')
                    
                    {{-- P.S. Don't forget the checkout button is probably hidden in the sidebar partial! Make it a bright, contrasting color. --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection