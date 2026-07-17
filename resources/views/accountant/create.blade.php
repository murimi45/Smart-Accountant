@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">{{ isset($accountant) ? 'Edit Accountant' : 'Add New Accountant' }}</h4>
                <p class="text-muted mb-0">{{ isset($accountant) ? 'Update accountant details' : 'Create a new accountant account' }}</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('accountants.index') }}" class="btn btn-outline-secondary">
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

    {{-- Accountant Form Card --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-user-tie me-2"></i>{{ isset($accountant) ? 'Accountant Details' : 'Accountant Information' }}
                </h5>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ isset($accountant) ? route('accountants.update', $accountant->id) : route('accountants.store') }}" 
                  method="POST" class="filter-form">
                @csrf
                @if(isset($accountant))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-user me-1"></i>Full Name</label>
                        <input type="text" name="admin_name" class="form-control" 
                               value="{{ old('admin_name', $accountant->admin_name ?? '') }}" required>
                        @error('admin_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-envelope me-1"></i>Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $accountant->email ?? '') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-phone me-1"></i>Phone Number</label>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ old('phone', $accountant->phone ?? '') }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6"></div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-lock me-1"></i>Password {{ isset($accountant) ? '(Leave blank to keep current)' : '' }}</label>
                        <input type="password" name="password" class="form-control" {{ isset($accountant) ? '' : 'required' }}>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-lock me-1"></i>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" {{ isset($accountant) ? '' : 'required' }}>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-2"></i>{{ isset($accountant) ? 'Update Accountant' : 'Create Accountant' }}
                    </button>
                    <a href="{{ route('accountants.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Base Variables — matched to Admin Management page */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
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
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.table-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.table-card .card-body {
    padding: 24px;
}

/* Form Elements */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.text-danger {
    display: block;
    margin-top: 4px;
    font-size: 12px;
}

/* Form actions divider */
.form-actions {
    padding-top: 20px;
    border-top: 1px solid var(--gray-200);
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

.btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
}

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
    padding: 8px 16px;
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
    .table-card .card-body {
        padding: 16px;
    }
}
</style>

@endsection