
<header class="coffee-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <!-- Logo -->
            <div class="col-md-3">
                <h1 class="logo">Perk Up Coffee</h1>
            </div>
            <!-- Search Bar -->
            <div class="col-md-6">
                <div class="search-container">
                    <input type="text" class="form-control search-input" placeholder="Search for drinks...">
                    <button class="search-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                </div>
            </div>
             <!-- Navigation Icons -->
           <div class="col-md-3">
            <div class="nav-icons">
                <a class="nav-icon-btn"
                href="{{ auth()->check() ? route('profile.user-profile') : route('login') }}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>{{ auth()->check() ? 'Account' : 'Login' }}</span>
                </a>
                                
                <a class="nav-icon-btn"
                    href="{{ route('order.history') }}"
                    data-bs-toggle="tooltip"
                    title="Orders">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Orders</span>
                    </a>
                                    
                    @include('dashboard.user-dashboard.partials.favorite-menu')
                
                @php $cartCount = collect(session('cart', []))->sum(); @endphp

                <a class="nav-icon-btn" href="{{ route('cart.show') }}">
                
                    {{-- icon + number in one box; the number is placed on the icon's corner --}}
                    <i style="position:relative; display:inline-block; line-height:0; font-style:normal;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                
                        <b id="cart-count"
                           style="position:absolute; top:-8px; right:-10px; min-width:18px; height:18px; padding:0 5px;
                                  border-radius:999px; background:#e53935; color:#fff; font-size:11px; font-weight:700;
                                  line-height:18px; text-align:center; border:2px solid #fff; box-sizing:content-box;
                                  {{ $cartCount < 1 ? 'display:none;' : '' }}">{{ $cartCount }}</b>
                    </i>
                
                    <span>Cart</span>
                </a>
            </div>
        </div>
          
        </div>
    </div>
</header>

