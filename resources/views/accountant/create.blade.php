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
        <div class="card-body">
            <form action="{{ isset($accountant) ? route('accountants.update', $accountant->id) : route('accountants.store') }}" 
                  method="POST">
                @csrf
                @if(isset($accountant))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="admin_name" class="form-control" 
                               value="{{ old('admin_name', $accountant->admin_name ?? '') }}" required>
                        @error('admin_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $accountant->email ?? '') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ old('phone', $accountant->phone ?? '') }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password {{ isset($accountant) ? '(Leave blank to keep current)' : '' }}</label>
                        <input type="password" name="password" class="form-control" {{ isset($accountant) ? '' : 'required' }}>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" {{ isset($accountant) ? '' : 'required' }}>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-2"></i>{{ isset($accountant) ? 'Update Accountant' : 'Create Accountant' }}
                    </button>
                    <a href="{{ route('accountants.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
