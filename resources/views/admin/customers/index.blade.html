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
    
    .badge-admin {
        background-color: var(--maroon); 
        color: white;
    }
    .badge-customer {
        background-color: #d1e7dd; 
        color: #0f5132;
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
                <h1 class="fw-bold m-0 dashboard-title">User List</h1>
                <button type="button" id='add-user-btn' class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#userModal">
                    <i class="bi bi-plus-lg me-1"></i> Add User
                </button>
            </div>
            
            {{-- User Table Card --}}
            <div class="card stat-card p-0 bg-white">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- $users is passed from the controller, representing customers --}}
                            @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="fw-medium">{{ $user->name }}</span></td>
                                <td class="text-muted small">{{ $user->email }}</td>
                                
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($user->role == 1) badge-admin
                                        @else badge-customer @endif">
                                        {{ $user->role == 1 ? 'Admin' : 'Customer' }}
                                    </span>
                                </td>
                                
                                
                                <td>
                                    {{-- Edit Button --}}
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-maroon edit-btn me-2"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#userModal"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->role ?? '0' }}" 
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    {{-- Delete Form (using customers.destroy route) --}}
                                    <form action="{{ route('customers.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
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
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 mb-2 d-block"></i>
                                    No customers found. Click "Add Customer" to create one.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Pagination Links (if $users is paginated) --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit User Modal --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content stat-card"> 
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark-brown" id="userModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{ route('customers.store') }}" id="user-form" method="post">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="user-id">

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="user@example.com" required>
                    </div>

                    {{-- 🌟 FIX: Add Role Selection Field --}}
                    <div class="mb-3">
                        <label for="role" class="form-label fw-medium">User Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="0">Customer</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    
                    {{-- Password Field (only required for creation) --}}
                    <div class="mb-4" id="password-group">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password on edit">
                        <small class="form-text text-muted">Required when creating a new customer.</small>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        {{-- Buttons toggle visibility via JS --}}
                        <button type="submit" id="update-btn" class="btn btn-warning rounded-pill d-none">Update Customer</button>
                        <button type="submit" id="save-btn" class="btn btn-maroon rounded-pill">Save Customer</button>
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
        const modal = document.getElementById('userModal');
        const modalTitle = document.getElementById('userModalLabel');
        const form = document.getElementById('user-form');
        const formMethod = document.getElementById('form-method');
        const userIdInput = document.getElementById('user-id');
        const saveBtn = document.getElementById('save-btn');
        const updateBtn = document.getElementById('update-btn');
        const addUserBtn = document.getElementById('add-user-btn');
        const passwordInput = document.getElementById('password');
        const passwordGroup = document.getElementById('password-group');
        // 🌟 FIX: Get the new role input
        const roleSelect = document.getElementById('role');

        // Base path for customers resource (e.g., /customers). 
        const customersBasePath = '{{ url("admin/customers") }}'; 
        const storeRoute = '{{ route("customers.store") }}';

        // Function to reset the form for "Add New Customer"
        function resetForm() {
            modalTitle.textContent = 'Add New Customer';
            form.action = storeRoute;
            formMethod.value = 'POST';
            form.reset();
            userIdInput.value = '';
            
            // 🌟 FIX: Reset role to default Customer (0)
            roleSelect.value = '0';
            
            // Show password field and make it required for creation
            passwordGroup.classList.remove('d-none');
            passwordInput.setAttribute('required', 'required');
            document.querySelector('#password-group small').textContent = 'Required when creating a new customer.';
            passwordInput.removeAttribute('placeholder');
            
            // Show Save button, hide Update button
            saveBtn.classList.remove('d-none');
            updateBtn.classList.add('d-none');
        }

        // 1. Handle "Add Customer" button click
        addUserBtn.addEventListener('click', resetForm);

        // 2. Handle "Edit" button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');

                modalTitle.textContent = `Edit Customer: ${name}`;
                
                // Set form action for PUT request (e.g., /admin/customers/1)
                form.action = `${customersBasePath}/${id}`; 
                formMethod.value = 'PUT'; 

                // Populate fields
                userIdInput.value = id;
                document.querySelector('#userModal #name').value = name;
                document.querySelector('#userModal #email').value = email;
                // 🌟 FIX: Populate the role select field
                roleSelect.value = role;

                // Configure password field for edit mode
                passwordGroup.classList.remove('d-none');
                passwordInput.value = ''; // Clear password field
                passwordInput.removeAttribute('required'); // Not required for update
                passwordInput.setAttribute('placeholder', 'Leave blank to keep current password');
                document.querySelector('#password-group small').textContent = 'Leave blank to keep the current password.';


                // Show Update button, hide Save button
                saveBtn.classList.add('d-none');
                updateBtn.classList.remove('d-none');
            });
        });

        // 3. Reset form when the modal is closed
        modal.addEventListener('hidden.bs.modal', resetForm);

    });
</script>