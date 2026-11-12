{{-- The Favorite icon in the header. Click it to open a panel listing the products you hearted.
     Put this where the old Favorite button was:
         @include('dashboard.user-dashboard.partials.favorite-menu')
     The script that fills and updates it is in favorite-ajax.blade.php --}}

     @php
     $favUser  = auth()->user();
     $favItems = $favUser
         ? $favUser->favorites()->orderBy('favorites.created_at', 'desc')->take(8)->get()
         : collect();
     $favCount = $favUser ? $favUser->favorites()->count() : 0;
 @endphp
 
 @once
 <style>
     .fav-menu { position: relative; display: inline-block; }
     .fav-menu button.nav-icon-btn { position: relative; }
 
     .fav-panel {
         display: none; position: absolute; top: calc(100% + 10px); right: 0;
         width: 340px; max-width: 92vw; z-index: 1500;
         background: #fff; border-radius: 16px; overflow: hidden; text-align: left;
         box-shadow: 0 10px 30px rgba(0, 0, 0, .18);
     }
     .fav-menu.open .fav-panel { display: block; }
 
     .fav-head { padding: .85rem 1rem; font-weight: 800; color: #2b2b2b; border-bottom: 1px solid #f0f0f0; }
     #fav-list { max-height: 320px; overflow-y: auto; }
     .fav-empty { padding: 1.4rem 1rem; margin: 0; text-align: center; color: #8a8a8a; font-size: .9rem; }
 
     .fav-row { display: flex; align-items: center; gap: .7rem; padding: .6rem 1rem; border-bottom: 1px solid #f6f6f6; }
     .fav-thumb img, .fav-noimg { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; display: block; background: #e9e9e9; }
     .fav-info { flex: 1 1 auto; min-width: 0; text-decoration: none; color: inherit; }
     .fav-name { font-weight: 700; font-size: .92rem; color: #2b2b2b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
     .fav-price { color: #843f3f; font-weight: 800; font-size: .88rem; }
     .fav-x { border: none; background: none; font-size: 1.4rem; line-height: 1; color: #b0b0b0; cursor: pointer; padding: 0 .3rem; }
     .fav-x:hover { color: #e53935; }
 
     .fav-all { display: block; text-align: center; padding: .8rem; font-weight: 700; color: #843f3f; text-decoration: none; background: #faf6f6; }
     .fav-all:hover { background: #f3e8e8; color: #6a3232; }
 </style>
 @endonce
 
 <div class="fav-menu" id="fav-menu">
 
     {{-- the icon, with the count on its top-right corner --}}
     <button type="button" class="nav-icon-btn" id="fav-trigger" aria-haspopup="true" aria-expanded="false">
         <i style="position:relative; display:inline-block; line-height:0; font-style:normal;">
             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                 <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
             </svg>
 
             <b id="fav-count"
                style="position:absolute; top:-8px; right:-10px; min-width:18px; height:18px; padding:0 5px;
                       border-radius:999px; background:#e53935; color:#fff; font-size:11px; font-weight:700;
                       line-height:18px; text-align:center; border:2px solid #fff; box-sizing:content-box;
                       {{ $favCount < 1 ? 'display:none;' : '' }}">{{ $favCount }}</b>
         </i>
         <span>Favorite</span>
     </button>
 
     {{-- the panel that opens under the icon --}}
     <div class="fav-panel" id="fav-panel">
         <div class="fav-head">My Favorites</div>
 
         <div id="fav-list">
             @guest
                 <p class="fav-empty">
                     <a href="{{ route('login') }}" style="color:#843f3f;font-weight:700;">Log in</a>
                     to save and see your favorites.
                 </p>
             @else
                 @forelse ($favItems as $fav)
                     <div class="fav-row">
                         <a href="{{ route('menu.show', $fav) }}" class="fav-thumb">
                             @if ($fav->image)
                                 <img src="{{ asset('storage/' . $fav->image) }}" alt="">
                             @else
                                 <span class="fav-noimg"></span>
                             @endif
                         </a>
                         <a href="{{ route('menu.show', $fav) }}" class="fav-info">
                             <div class="fav-name">{{ $fav->name }}</div>
                             <div class="fav-price">${{ number_format($fav->price, 2) }}</div>
                         </a>
                         <button type="button" class="fav-x favorite-btn active"
                                 data-url="{{ route('favorites.toggle', $fav) }}"
                                 aria-label="Remove from favorites">&times;</button>
                     </div>
                 @empty
                     <p class="fav-empty">No favorites yet.<br>Tap the heart on a product to save it.</p>
                 @endforelse
             @endguest
         </div>
 
         @auth
            <a href="{{ route('favorites.index') }}" class="btn btn-sm w-100 text-white mt-2"
            style="background:#843f3f;border-radius:999px;">
            View all favorites
        </a>
         @endauth
     </div>
 </div>