@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">{{ isset($admin) ? 'Edit Admin' : 'Add New Admin' }}</h4>
                <p class="text-muted mb-0">{{ isset($admin) ? 'Update admin details' : 'Create a new admin account' }}</p>
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

    {{-- Admin Form Card --}}
    <div class="card table-card">
        <div class="card-body">
            <form action="{{ isset($admin) ? route('admins.update', $admin->id) : route('admins.store') }}" 
                  method="POST">
                @csrf
                @if(isset($admin))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="admin_name" class="form-control" 
                               value="{{ old('name', $admin->admin_name ?? '') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $admin->email ?? '') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ old('phone', $admin->phone ?? '') }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password {{ isset($admin) ? '(Leave blank to keep current)' : '' }}</label>
                        <input type="password" name="password" class="form-control" {{ isset($admin) ? '' : 'required' }}>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" {{ isset($admin) ? '' : 'required' }}>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="admin" {{ (old('role', $admin->role ?? '') == 'admin') ? 'selected' : '' }}>Admin</option>
                            <option value="Accountant" {{ (old('role', $admin->role ?? '') == 'Accountant') ? 'selected' : '' }}>Accountant</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-2"></i>{{ isset($admin) ? 'Update Admin' : 'Create Admin' }}
                    </button>
                    <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
