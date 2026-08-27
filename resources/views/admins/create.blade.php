@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">{{ isset($admin) ? 'Edit User' : 'Add New User' }}</h4>
                <p class="text-muted mb-0">{{ isset($admin) ? 'Update user details' : 'Create a school user (admin, accountant, teacher, or HR manager)' }}</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- User Form Card --}}
    <div class="card form-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-user-circle me-2"></i>{{ isset($admin) ? 'User Details' : 'New User Details' }}
                </h5>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ isset($admin) ? route('admins.update', $admin->id) : route('admins.store') }}"
                  method="POST">
                @csrf
                @if(isset($admin))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-user me-1"></i>Full Name</label>
                        <input type="text" name="admin_name" class="form-control"
                               placeholder="Enter full name"
                               value="{{ old('name', $admin->admin_name ?? '') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-envelope me-1"></i>Email</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="Enter email address"
                               value="{{ old('email', $admin->email ?? '') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-phone me-1"></i>Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="Enter phone number"
                               value="{{ old('phone', $admin->phone ?? '') }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-user-tag me-1"></i>Role</label>
                        @php $currentRole = old('role', isset($admin) ? strtolower((string) $admin->role) : ''); @endphp
                        <select name="role" class="form-select" required>
                            <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="accountant" {{ $currentRole === 'accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="teacher" {{ $currentRole === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="hr_manager" {{ $currentRole === 'hr_manager' ? 'selected' : '' }}>HR Manager</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-lock me-1"></i>Password {{ isset($admin) ? '(Leave blank to keep current)' : '' }}</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="{{ isset($admin) ? 'Leave blank to keep current' : 'Enter password' }}"
                               {{ isset($admin) ? '' : 'required' }}>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-lock me-1"></i>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Confirm password"
                               {{ isset($admin) ? '' : 'required' }}>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-2"></i>{{ isset($admin) ? 'Update User' : 'Create User' }}
                    </button>
                    <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Base Variables — matched to Term Management / Users List page */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

/* Page Header */
.page-header h4 {
    font-size: 24px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.page-header p {
    font-size: 14px;
    color: var(--gray-500);
}

/* Card */
.form-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.form-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.form-card .card-body {
    padding: 24px 20px;
}

/* Form Elements */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control,
.form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.form-actions {
    padding-top: 16px;
    border-top: 1px solid var(--gray-100);
}

/* Buttons */
.btn {
    border-radius: var(--border-radius);
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #2a8cbd;
    border-color: #2a8cbd;
}

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
    padding: 8px 12px;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-700);
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 12px 16px;
}

.alert-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card .card-body {
        padding: 20px 16px;
    }
}
</style>

@endsection