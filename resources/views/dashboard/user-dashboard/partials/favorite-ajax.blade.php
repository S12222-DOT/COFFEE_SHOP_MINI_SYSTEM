{{-- Heart buttons + Favorite icon panel.
     - every button with class  favorite-btn  and a  data-url  toggles a favorite without leaving the page
     - the panel under the Favorite icon (favorite-menu partial) is refreshed from the server's answer
     Include cart-ajax.blade.php on the same page first (it holds the message CSS). --}}

     @once
     <style>
         /* filled heart when the product is a favorite */
         .pc-fav.active { background: #fdecec; color: #e53935; border-color: #e53935; }
         .pc-fav.active svg { fill: currentColor; }
         .favorite-btn:disabled { opacity: .6; cursor: wait; }
         #fav-count.bump { animation: favBump .35s ease; }
         @keyframes favBump { 0% { transform: scale(1); } 50% { transform: scale(1.5); } 100% { transform: scale(1); } }
     </style>
     @endonce
     
     <script>
     (function () {
         var LOGIN_URL = @json(route('login'));
         var CSRF      = @json(csrf_token());
     
         function esc(s) {
             var d = document.createElement('div');
             d.textContent = s == null ? '' : s;
             return d.innerHTML;
         }
     
         function toast(message, type) {
             var el = document.createElement('div');
             el.className = 'cart-toast ' + (type || 'ok');
             el.textContent = message;
             document.body.appendChild(el);
             requestAnimationFrame(function () { el.classList.add('show'); });
             setTimeout(function () {
                 el.classList.remove('show');
                 setTimeout(function () { el.remove(); }, 250);
             }, 2500);
         }
     
         function updateBadge(count) {
             var badge = document.getElementById('fav-count');
             if (!badge) return;
             badge.textContent = count;
             badge.style.display = count > 0 ? '' : 'none';
             badge.classList.remove('bump');
             void badge.offsetWidth;
             badge.classList.add('bump');
         }
     
         // Rebuild the list inside the panel under the Favorite icon
         function renderPanel(favs) {
             var list = document.getElementById('fav-list');
             if (!list) return;
     
             if (!favs.length) {
                 list.innerHTML = '<p class="fav-empty">No favorites yet.<br>Tap the heart on a product to save it.</p>';
                 return;
             }
     
             list.innerHTML = favs.map(function (f) {
                 var img = f.image
                     ? '<img src="' + esc(f.image) + '" alt="">'
                     : '<span class="fav-noimg"></span>';
                 return '<div class="fav-row">'
                     + '<a href="' + esc(f.url) + '" class="fav-thumb">' + img + '</a>'
                     + '<a href="' + esc(f.url) + '" class="fav-info">'
                     +   '<div class="fav-name">' + esc(f.name) + '</div>'
                     +   '<div class="fav-price">$' + esc(f.price) + '</div>'
                     + '</a>'
                     + '<button type="button" class="fav-x favorite-btn active" data-url="' + esc(f.toggle_url) + '" aria-label="Remove from favorites">&times;</button>'
                     + '</div>';
             }).join('');
         }
     
         // Keep every heart for this product in sync (cards, detail page, favorites page)
         function syncHearts(url, favorited) {
             document.querySelectorAll('.pc-fav.favorite-btn').forEach(function (b) {
                 if (b.dataset.url !== url) return;
                 b.classList.toggle('active', favorited);
                 var item = b.closest('[data-fav-item]');          // on the Favorites page
                 if (item && !favorited) item.remove();
             });
         }
     
         // ---- open / close the panel ----
         var trigger = document.getElementById('fav-trigger');
         var menu    = document.getElementById('fav-menu');
         if (trigger && menu) {
             trigger.addEventListener('click', function (e) {
                 e.stopPropagation();
                 var open = menu.classList.toggle('open');
                 trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
             });
             document.addEventListener('click', function (e) {
                 if (!menu.contains(e.target)) menu.classList.remove('open');
             });
             document.addEventListener('keydown', function (e) {
                 if (e.key === 'Escape') menu.classList.remove('open');
             });
         }
     
         // ---- heart / remove clicks ----
         document.addEventListener('click', function (e) {
             var btn = e.target.closest ? e.target.closest('.favorite-btn') : null;
             if (!btn || !btn.dataset.url) return;
     
             e.preventDefault();
             btn.disabled = true;
             var url = btn.dataset.url;
     
             fetch(url, {
                 method: 'POST',
                 credentials: 'same-origin',
                 headers: {
                     'Accept': 'application/json',
                     'X-Requested-With': 'XMLHttpRequest',
                     'X-CSRF-TOKEN': CSRF
                 }
             })
             .then(function (res) {
                 if (res.status === 401) { window.location = LOGIN_URL; return null; }   // not logged in
                 if (res.status === 419) { window.location.reload(); return null; }      // page expired
                 return res.json().then(function (data) { return { ok: res.ok, data: data }; });
             })
             .then(function (result) {
                 if (!result) return;
                 if (result.ok && result.data.success) {
                     syncHearts(url, result.data.favorited);
                     updateBadge(result.data.favorite_count);
                     renderPanel(result.data.favorites || []);
                     toast(result.data.message, 'ok');
                 } else {
                     toast(result.data.message || 'Could not update favorites.', 'err');
                 }
             })
             .catch(function () { toast('Network error. Please try again.', 'err'); })
             .finally(function () { btn.disabled = false; });
         });
     })();
     </script>