<style>
    /* Base CSS Variables for consistency (pulled from navbar styles) */
    :root {
        --maroon: #A6003E;
        --maroon-dark: #7a0019;
        --dark-brown: #333333;
        --shadow-light: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .stat-card {
        border-radius: 20px;
        box-shadow: var(--shadow-light);
    }
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
        color: white;
    }
    .rounded-pill {
        border-radius: 50rem !important;
    }
    .dashboard-title {
        font-weight: 800;
    }
    .text-maroon {
        color: var(--maroon);
    }
    .form-control {
        border-radius: 12px;
        padding: 10px 15px;
        border-color: #e0e0e0;
    }
    
</style>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content stat-card">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title dashboard-title text-maroon" id="logoutModalLabel">Confirm Log Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-1 pb-4">
                <p>Are you sure you want to end your current session?</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-maroon rounded-pill">Log Out</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Profile Edit Modal -->
@auth
<div class="modal fade" id="profileEditModal" tabindex="-1" aria-labelledby="profileEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content stat-card">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title dashboard-title text-maroon" id="profileEditModalLabel">Edit Your Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-1 pb-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Name Field --}}
                    <div class="mb-3">
                        <label for="profileName" class="form-label fw-medium">Name</label>
                        <input type="text" class="form-control" id="profileName" name="name" 
                               value="{{ Auth::user()->name ?? '' }}" required>
                    </div>

                    {{-- Email Field --}}
                    <div class="mb-4">
                        <label for="profileEmail" class="form-label fw-medium">Email Address</label>
                        <input type="email" class="form-control" id="profileEmail" name="email" 
                               value="{{ Auth::user()->email ?? '' }}" required>
                    </div>

                    {{-- Password Fields (Optional for security) --}}
                    <h6 class="fw-bold mb-3 text-dark-brown">Change Password (Optional)</h6>
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" placeholder="Enter current password">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="password" placeholder="Enter new password">
                    </div>
                    <div class="mb-4">
                        <label for="newPasswordConfirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="newPasswordConfirmation" name="password_confirmation" placeholder="Confirm new password">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-maroon rounded-pill">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth
