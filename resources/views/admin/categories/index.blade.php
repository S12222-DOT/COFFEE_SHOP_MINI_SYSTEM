@extends('layouts.app') 
@section('content')
<style>
    :root {
        --maroon: #A6003E; /* Updated: Slightly brighter maroon */
        --maroon-dark: #7a0019;
        --dark-brown: #333333;
        --bg-light: #f4f7f9; /* Soft background */
        --accent-teal: #00bcd4; /* Gen Z Pop Color */
        --shadow-light: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    body {
        background-color: var(--bg-light);
        font-family: 'Inter', sans-serif; /* Modern sans-serif */
    }
    .dashboard-wrapper {
        min-height: 100vh;
    }
    .dashboard-title {
        color: var(--dark-brown);
        font-weight: 800;
        letter-spacing: -1px;
    }
    /* Custom Button Styles (Chunky/Pill) */
    .btn-maroon {
        background-color: var(--maroon);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 50px; /* Ultra-rounded */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 6px rgba(166, 0, 62, 0.3);
    }
    .btn-maroon:hover {
        background-color: var(--maroon-dark);
        box-shadow: 0 6px 12px rgba(166, 0, 62, 0.4);
        transform: translateY(-1px);
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
    .stat-card {
        border-radius: 20px; /* Large radius */
        box-shadow: var(--shadow-light);
        border: none;
        overflow: hidden; /* For table border radius */
    }
    /* Table Styling */
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
    .modal-content {
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .form-control, .form-select {
        border-radius: 12px;
        border-color: #e0e0e0;
        padding: 10px 15px;
    }
    .modal-footer button {
        border-radius: 50px !important;
    }
</style>
<div class="dashboard-wrapper"> 
    <div class="main-content">
        <div class="container-fluid p-5">
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
                <h1 class="fw-bold m-0 dashboard-title">Category List</h1>
                <button type="button" id='add-category-btn' class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </button>
            </div>
            
            {{-- Category Table Card --}}
            <div class="card stat-card p-0 bg-white">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="fw-medium">{{ $category->name }}</span></td>
                                <td class="text-muted small">{{ Str::limit($category->description, 70) }}</td>
                                <td>
                                    {{-- Edit Button --}}
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-maroon edit-btn me-2"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#categoryModal"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-description="{{ $category->description }}"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    {{-- Delete Form --}}
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn rounded-pill" style="--bs-btn-padding-y: .375rem; --bs-btn-padding-x: .75rem;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-6 mb-2 d-block"></i>
                                    No categories found. Click "Add Category" to get started.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Category Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content stat-card"> 
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark-brown" id="categoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{ route('categories.store') }}" id="category-form" method="post">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="category-id">

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="E.g., Coffee, Pastries, Merchandise" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-medium">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="A brief description of this category."></textarea>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        {{-- Buttons toggle visibility via JS --}}
                        <button type="submit" id="update-btn" class="btn btn-warning rounded-pill d-none">Update Category</button>
                        <button type="submit" id="save-btn" class="btn btn-maroon rounded-pill">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Custom JavaScript Logic for Add/Edit Modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('categoryModal');
        const modalTitle = document.getElementById('categoryModalLabel');
        const form = document.getElementById('category-form');
        const formMethod = document.getElementById('form-method');
        const categoryIdInput = document.getElementById('category-id');
        const saveBtn = document.getElementById('save-btn');
        const updateBtn = document.getElementById('update-btn');
        const addCategoryBtn = document.getElementById('add-category-btn');

        // Base URL for categories (adjust if your dashboard path is different)
        // This is used to construct the PUT route for editing: /admin/categories/{id}
        const categoriesBaseUrl = '{{ url("admin/categories") }}';

        // Function to reset the form for "Add New Category"
        function resetForm() {
            modalTitle.textContent = 'Add New Category';
            form.action = "{{ route('categories.store') }}";
            formMethod.value = 'POST';
            form.reset();
            categoryIdInput.value = '';
            
            // Show Save button, hide Update button
            saveBtn.classList.remove('d-none');
            updateBtn.classList.add('d-none');
        }

        // 1. Handle "Add Category" button click
        // Ensures the form is clean and ready for a POST request
        addCategoryBtn.addEventListener('click', resetForm);

        // 2. Handle "Edit" button click
        // Populates the form fields and sets the form action for a PUT request
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');

                modalTitle.textContent = `Edit: ${name}`;
                
                // Set form action for PUT request (e.g., /admin/categories/1)
                form.action = `${categoriesBaseUrl}/${id}`; 
                formMethod.value = 'PUT'; 

                // Populate fields
                categoryIdInput.value = id;
                document.querySelector('#categoryModal #name').value = name;
                document.querySelector('#categoryModal #description').value = description;

                // Show Update button, hide Save button
                saveBtn.classList.add('d-none');
                updateBtn.classList.remove('d-none');
            });
        });

        // 3. Reset form when the modal is closed
        // Ensures that the next time the modal opens (whether for Add or Edit), it starts fresh.
        modal.addEventListener('hidden.bs.modal', resetForm);
        
        // Note: The delete button relies on the standard form submission. 
        // For a Gen Z feel, you might implement a more elaborate "Are you sure?" 
        // confirmation modal here instead of relying on the browser default. 
        // However, sticking to existing form behavior for stability.
    });
</script>
