{{-- 
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 p-4 md:p-8 bg-gray-50 rounded-lg" id="productGrid">
    @forelse($products as $product)
        @include('dashboard.user-dashboard.partials.product-card', ['product' => $product])
    @empty
        <p class="col-span-full text-center text-lg text-gray-500 py-10">No products are currently available.</p>
    @endforelse
</div> --}}
{{-- Product grid: 4 per row on desktop. Styles are self-contained, so they work with or without Bootstrap/Tailwind. --}}
<style>
    .menu-products {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.25rem;
        padding: 0.5rem;
    }
    @media (max-width: 1100px) { .menu-products { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 800px)  { .menu-products { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 420px)  { .menu-products { grid-template-columns: 1fr; } }
    .menu-products .menu-empty { grid-column: 1 / -1; text-align: center; color: #6b7280; padding: 2.5rem 0; font-size: 1.1rem; }
</style>

<div class="menu-products" id="productGrid">
    @forelse ($products as $product)
        @include('dashboard.user-dashboard.partials.product-card', ['product' => $product])
    @empty
        <p class="menu-empty">No products are currently available.</p>
    @endforelse
</div>
