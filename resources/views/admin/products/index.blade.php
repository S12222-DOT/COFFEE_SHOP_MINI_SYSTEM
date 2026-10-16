@extends('layouts.app') 
@section('content')
<style>
    :root {
        --maroon: #A6003E; /* Updated: Slightly brighter maroon */
        --maroon-dark: #7a0019;
        --dark-brown: #333333;
        --bg-light: #f4f7f9; /* Soft background */
        --shadow-light: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-deep: 0 6px 12px rgba(166, 0, 62, 0.4);
    }
    /* BASE STYLES */
    body {
        background-color: var(--bg-light);
        font-family: 'Inter', sans-serif;
    }
    .dashboard-title {
        color: var(--dark-brown);
        font-weight: 800;
        letter-spacing: -1px;
    }
    /* CARD & MODAL STYLES */
    .stat-card {
        border-radius: 20px; /* Large radius */
        box-shadow: var(--shadow-light);
        border: none;
        overflow: hidden;
    }
    .modal-content {
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .form-control, .form-select {
        border-radius: 12px;
        border-color: #e0e0e0;
        padding: 10px 15px;
    }
    /* BUTTON STYLES (Chunky/Pill) */
    .btn-maroon {
        background-color: var(--maroon);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 50px;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 6px rgba(166, 0, 62, 0.3);
    }
    .btn-maroon:hover {
        background-color: var(--maroon-dark);
        box-shadow: var(--shadow-deep);
        transform: translateY(-1px);
        color: white;
    }
    .btn-outline-maroon {
        color: var(--maroon);
        border-color: var(--maroon);
        border-radius: 12px;
        transition: background-color 0.2s;
    }
    .btn-outline-maroon:hover {
        background-color: var(--maroon);
        color: white;
    }
    .btn-danger {
        border-radius: 50px !important;
    }
    .modal-footer button {
        border-radius: 50px !important;
    }

    /* TABLE STYLING */
    .table thead th {
        background-color: var(--dark-brown) !important;
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem 1.5rem;
    }
    .table tbody tr {
        transition: background-color 0.15s ease-in-out;
        border-bottom: 1px solid #eeeeee;
    }
    .table tbody tr:hover {
        background-color: #ffe0e020; /* Subtle hover color */
    }
    .table td {
        padding: 1rem 1.5rem;
    }
    /* IMAGE STYLING */
    .product-image {
        border: 2px solid #e0e0e0;
        transition: box-shadow 0.2s;
    }
    .product-image:hover {
        box-shadow: 0 0 0 3px rgba(166, 0, 62, 0.2);
    }
    /* Stock Badge Clarity */
    .badge {
        font-weight: 600;
        padding: 0.5em 0.8em;
        border-radius: 50px;
        min-width: 60px;
    }
</style>

<div class="dashboard-wrapper"> 
    <div class="main-content">
        <div class="container-fluid p-5">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header and Add Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold m-0 dashboard-title text-dark-brown">Product List</h1>
                <button type="button" id='add-product-btn' class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Product
                </button>
            </div>
            
            {{-- Product Table Card --}}
            <div class="card stat-card p-0 bg-white">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @php
                                        $imageSrc = $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/50x50/A6003E/ffffff?text=Prod';
                                    @endphp
                                    <img src="{{ $imageSrc }}" alt="{{ $product->name }}" width="150" height="150" class="rounded product-image object-fit-cover">
                                </td>
                                <td><span class="fw-medium">{{ $product->name }}</span></td>
                                <td class="text-muted">{{ $product->category->name ?? 'N/A' }}</td>
                                <td><span class="fw-semibold text-success">${{ number_format($product->price, 2) }}</span></td>
                                <td>
                                    {{-- Stock badge logic --}}
                                    <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>
                                    {{-- Edit Button --}}
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-maroon edit-btn me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#productModal"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-description="{{ $product->description }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}"
                                        data-category-id="{{ $product->category_id }}"
                                        data-image="{{ $imageSrc }}" {{-- Use the resolved path for JS --}}
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    {{-- Delete Form --}}
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn" style="--bs-btn-padding-y: .375rem; --bs-btn-padding-x: .75rem;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box2 display-6 mb-2 d-block"></i>
                                    No products found. Click "Add Product" to create one!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Product Modal --}}
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content stat-card"> 
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark-brown" id="productModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{ route('products.store') }}" id="product-form" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- Hidden field to override HTTP method for PUT/PATCH requests --}}
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="product-id">

                    {{-- Image Upload and Preview --}}
                    <div class="mb-3 text-center">
                        <label for="image" class="form-label d-block fw-medium">Product Image</label>
                        <img id="product-img-preview" class="rounded object-fit-cover product-image shadow-sm" 
                             src="https://i.pinimg.com/736x/9c/83/bc/9c83bc3ef5c1431c52433715936c7a03.jpg" 
                             width="120px" height="120px" alt="Product Image" style="cursor: pointer;">
                        <input type="file" id="image-upload" class="form-control d-none" name="image" accept="image/*">
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Product Name" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-medium">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Select Category</option>
                            @foreach ($category as $cate)
                                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-medium">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-medium">Stock</label> 
                            <input type="number" class="form-control" id="stock" name="stock" placeholder="0" min="0" required> 
                        </div>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="update-btn" class="btn btn-warning rounded-pill d-none">Update Product</button>
                        <button type="submit" id="save-btn" class="btn btn-maroon rounded-pill">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('productModal');
        const modalTitle = document.getElementById('productModalLabel');
        const form = document.getElementById('product-form');
        const formMethod = document.getElementById('form-method'); // The hidden _method field
        const productIdInput = document.getElementById('product-id');
        const saveBtn = document.getElementById('save-btn');
        const updateBtn = document.getElementById('update-btn');
        const addProductBtn = document.getElementById('add-product-btn');
        
        const imagePreview = document.getElementById('product-img-preview');
        const imageUploadInput = document.getElementById('image-upload');
        const defaultPlaceholder = 'https://i.pinimg.com/736x/9c/83/bc/9c83bc3ef5c1431c52433715936c7a03.jpg';

        const productsBaseUrl = '{{ url("admin/products") }}'; // Using 'products' route name prefix for generality
        const storeRoute = '{{ route("products.store") }}';

        function resetForm() {
            modalTitle.textContent = 'Add New Product';
            form.action = storeRoute;
            formMethod.value = 'POST'; // Set to POST for creation
            form.reset();
            productIdInput.value = '';
            imagePreview.src = defaultPlaceholder;
            
            // Show Save button, hide Update button
            saveBtn.classList.remove('d-none');
            updateBtn.classList.add('d-none');
            // Reset image input value
            imageUploadInput.value = null;
            // Remove any existing errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        // --- Image Preview Logic ---
        // Click the image preview to open the file selector
        imagePreview.addEventListener('click', () => {
            imageUploadInput.click();
        });

        // Display the selected image
        imageUploadInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // 1. Handle "Add Product" button click
        addProductBtn.addEventListener('click', resetForm);

        // 2. Handle "Edit" button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Clear the image upload input to prevent sending old file data on update
                imageUploadInput.value = null; 

                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const price = this.getAttribute('data-price');
                const stock = this.getAttribute('data-stock');
                const categoryId = this.getAttribute('data-category-id');
                const imageUrl = this.getAttribute('data-image'); // This is the image src

                modalTitle.textContent = `Edit Product: ${name}`;
                
                // Set form action for PUT request (Laravel expects POST to this URL)
                form.action = `${productsBaseUrl}/${id}`; 
                // *** CRITICAL FIX: Set the hidden _method field to PUT for Laravel to handle it as an update ***
                formMethod.value = 'PUT'; 

                // Populate fields
                productIdInput.value = id;
                document.querySelector('#productModal #name').value = name;
                document.querySelector('#productModal #price').value = parseFloat(price).toFixed(2);
                document.querySelector('#productModal #stock').value = stock;
                document.querySelector('#productModal #category_id').value = categoryId;
                
                // Set image preview
                imagePreview.src = imageUrl || defaultPlaceholder;
                
                // Show Update button, hide Save button
                saveBtn.classList.add('d-none');
                updateBtn.classList.remove('d-none');
            });
        });

        // 3. Reset form when the modal is closed
        // This ensures the next time the modal opens (for Add or Edit), it starts clean.
        modal.addEventListener('hidden.bs.modal', resetForm);
    });
</script>
