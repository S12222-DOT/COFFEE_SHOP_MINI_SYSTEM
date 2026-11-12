{{-- Order summary on the right of the menu page: shows what is in the cart (read from the session).
     NOTE: the variables here start with $sb so they never overwrite the page's own $products / $product. --}}
     @php
     $sbCart     = session('cart', []);
     $sbProducts = $sbCart
         ? \App\Models\Product::whereIn('id', array_keys($sbCart))->get()->keyBy('id')
         : collect();
 
     $sbItems   = [];
     $sbTotal   = 0;
     $sbProblem = false;     // true when an item has more quantity than stock
 
     foreach ($sbCart as $sbId => $sbQty) {
         $sbProduct = $sbProducts->get($sbId);
         if (!$sbProduct) continue;
 
         $sbLine   = $sbProduct->price * $sbQty;
         $sbTotal += $sbLine;
         if ($sbProduct->stock < $sbQty) $sbProblem = true;
 
         $sbItems[] = ['product' => $sbProduct, 'qty' => $sbQty, 'line' => $sbLine];
     }
 @endphp
 
 @once
 <style>
     .sb-thumb { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #e9e9e9; flex-shrink: 0; }
     .order-items-list { max-height: 360px; overflow-y: auto; }
     .sb-empty { text-align: center; color: #8a8a8a; padding: 1.5rem .5rem; }
     .sb-link { display: block; text-align: center; margin-top: .7rem; color: #843f3f; font-weight: 600; text-decoration: none; }
     .sb-link:hover { text-decoration: underline; color: #6a3232; }
 </style>
 @endonce
 
 <div class="order-sidebar p-4 h-100" id="order-sidebar">
     <h3 class="order-title mb-4">Order</h3>
 
     {{-- Items in the cart --}}
     <div class="order-items-list mb-4">
         @forelse ($sbItems as $row)
             <div class="d-flex align-items-center justify-content-between order-item p-2 mb-2">
                 <div class="d-flex align-items-center">
                     @if ($row['product']->image)
                         <img class="sb-thumb" src="{{ asset('storage/' . $row['product']->image) }}" alt="">
                     @else
                         <span class="sb-thumb d-inline-block"></span>
                     @endif
 
                     <span class="ms-3 item-name">
                         {{ $row['product']->name }}
                         <small class="text-muted d-block">${{ number_format($row['product']->price, 2) }} &times; {{ $row['qty'] }}</small>
                     </span>
                 </div>
                 <span class="item-price">${{ number_format($row['line'], 2) }}</span>
             </div>
         @empty
             <div class="sb-empty">
                 Your order is empty.<br>
                 Tap <b>Add to Cart</b> on a product.
             </div>
         @endforelse
     </div>
 
     {{-- Totals and checkout --}}
     <div class="order-summary mt-5 pt-3">
         <div class="d-flex justify-content-between summary-line">
             <span>Items :</span>
             <span class="fw-bold">{{ collect($sbCart)->sum() }}</span>
         </div>
         <hr>
         <div class="d-flex justify-content-between total-line">
             <span>Total :</span>
             <span class="total-price">${{ number_format($sbTotal, 2) }}</span>
         </div>
 
         <div class="text-center mt-4">
             @if (empty($sbItems))
                 <button type="button" class="btn btn-lg pay-now-btn w-100" disabled>Checkout</button>
             @elseif ($sbProblem)
                 <a href="{{ route('cart.show') }}" class="btn btn-lg pay-now-btn w-100">Fix stock in cart</a>
             @else
                 @auth
                     <form method="POST" action="{{ route('order.place') }}">
                         @csrf
                         <button type="submit" class="btn btn-lg pay-now-btn w-100">Checkout</button>
                     </form>
                 @else
                     <a href="{{ route('login') }}" class="btn btn-lg pay-now-btn w-100">Log in to checkout</a>
                 @endauth
             @endif
 
             @if (!empty($sbItems))
                 <a href="{{ route('cart.show') }}" class="sb-link">View / edit cart</a>
             @endif
         </div>
     </div>
 </div>