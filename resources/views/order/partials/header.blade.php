<nav class="navbar navbar-expand-lg py-3 header-bar">
    <div class="container-fluid px-5">
        <a class="navbar-brand brand-text" href="#">Perk Up Coffee</a>
        
        <div class="d-flex align-items-center">
            <form class="d-flex mx-4 search-form">
                <input class="form-control me-2 search-input" type="search" placeholder="" aria-label="Search">
                <button class="btn search-btn" type="submit">
                    <i class="bi bi-search"></i> 
                    {{-- Use Bootstrap Icons (bi-search) --}}
                </button>
            </form>
            
           <!-- Navigation Icons -->
           <div class="col-md-3">
            <div class="nav-icons">
                <button class="nav-icon-btn" data-bs-toggle="tooltip" title="Account">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Account</span>
                </button>
                
                <button class="nav-icon-btn" <a class="nav-icon-btn"
                        href="{{route('order.history') }}" data-bs-toggle="tooltip" title="Orders">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Orders</span>
                </button>
                
                <button class="nav-icon-btn" data-bs-toggle="tooltip" title="Favorite">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span>Favorite</span>
                </button>
                
                <button class="nav-icon-btn" data-bs-toggle="tooltip" title="Cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <span>Cart</span>
                </button>
            </div>
        </div>
        </div>
    </div>
</nav>