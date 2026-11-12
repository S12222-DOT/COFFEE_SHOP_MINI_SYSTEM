{{-- Category tabs: built from your real categories (admin > Category) and filter the product cards on this page.
     Also makes the header search box (.search-input) filter the cards. --}}
     <style>
        .ct-tabs { display: flex; flex-wrap: wrap; gap: .9rem; margin-bottom: .5rem; }
        .ct-tab {
            border: none; cursor: pointer;
            padding: .8rem 1.8rem; border-radius: 20px;
            background: #f3e3d3; color: #843f3f;
            font-size: 1.15rem; font-weight: 800;
            box-shadow: 0 3px 8px rgba(0, 0, 0, .08);
            transition: all .15s ease;
        }
        .ct-tab:hover { transform: translateY(-2px); }
        .ct-tab.is-active { background: #fff; box-shadow: 0 6px 14px rgba(132, 63, 63, .25); }
    </style>
    
    <div class="ct-tabs" id="categoryTabs">
        <button type="button" class="ct-tab is-active" data-filter="all">All</button>
        @foreach ($categories as $category)
            <button type="button" class="ct-tab" data-filter="{{ $category->id }}">{{ $category->name }}</button>
        @endforeach
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('#categoryTabs .ct-tab');
            const searchInput = document.querySelector('.search-input');
            let currentFilter = 'all';
            let searchTerm = '';
    
            function applyFilters() {
                // Cards come from partials/product-card.blade.php (.pc-card, data-category = category id)
                const cards = document.querySelectorAll('#productGrid .pc-card');
                let visible = 0;
    
                cards.forEach(card => {
                    const name = (card.querySelector('.pc-name')?.textContent || '').toLowerCase();
                    const inCategory = currentFilter === 'all' || card.dataset.category === currentFilter;
                    const matchesSearch = name.includes(searchTerm);
                    const show = inCategory && matchesSearch;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
    
                let empty = document.getElementById('ctEmpty');
                if (!empty) {
                    empty = document.createElement('p');
                    empty.id = 'ctEmpty';
                    empty.style.cssText = 'grid-column:1/-1;text-align:center;color:#6b7280;padding:2rem 0;display:none;';
                    empty.textContent = 'No products found.';
                    document.getElementById('productGrid').appendChild(empty);
                }
                empty.style.display = visible === 0 ? 'block' : 'none';
            }
    
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('is-active'));
                    this.classList.add('is-active');
                    currentFilter = this.dataset.filter;
                    applyFilters();
                });
            });
    
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    searchTerm = this.value.trim().toLowerCase();
                    applyFilters();
                });
            }
        });
    </script>   