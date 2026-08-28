@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Add New Fee</h4>
                <p class="text-muted mb-0">Create a new fee structure for a class</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('classfeelist') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card form-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-money-bill-wave me-2"></i>Fee Amount Information
                </h5>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('insertclassfee') }}" method="post" enctype="multipart/form-data">
                @csrf

                {{-- Class & Term Information Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-graduation-cap me-2"></i>Class & Term Information
                    </h6>

                    <div class="row g-3">
                        {{-- Grade --}}
                        <div class="col-md-6">
                            <label for="class_id" class="form-label">
                                <i class="fa fa-school me-1"></i>Grade <span class="text-danger">*</span>
                            </label>
                            <select id="class_id"
                                    name="class_id"
                                    class="form-select @error('class_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Grade</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Term --}}
                        <div class="col-md-6">
                            <label for="term_id" class="form-label">
                                <i class="fa fa-calendar me-1"></i>Term <span class="text-danger">*</span>
                            </label>
                            <select id="term_id"
                                    name="term_id"
                                    class="form-select @error('term_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }} - {{ $term->year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Fee Details Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-dollar-sign me-2"></i>Fee Details
                    </h6>

                    <div class="row g-3">
                        {{-- Amount --}}
                        <div class="col-md-6">
                            <label for="amount" class="form-label">
                                <i class="fa fa-coins me-1"></i>Amount (KSh) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   placeholder="Enter amount"
                                   step="0.01"
                                   min="0"
                                   required>
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted mt-2 d-block">
                                <i class="fa fa-info-circle me-1"></i>Enter the fee amount in Kenya Shillings
                            </small>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                <i class="fa fa-toggle-on me-1"></i>Status <span class="text-danger">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted mt-2 d-block">
                                <i class="fa fa-info-circle me-1"></i>Active fees will be visible to students
                            </small>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="form-label">
                                <i class="fa fa-file-alt me-1"></i>Description <span class="text-danger">*</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Enter fee description (e.g., Tuition fees for Term 1, includes books and materials)"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check-circle me-2"></i>Create Fee
                    </button>
                    <a href="{{ route('classfeelist') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fa fa-times-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Base Variables — matched to Users List / standard system pages */
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

/* Form Sections */
.form-section {
    background: var(--gray-50);
    padding: 20px;
    border-radius: var(--border-radius);
    border: 1px solid var(--gray-100);
}

.section-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    padding-bottom: 12px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--gray-200);
}

.section-title i {
    color: var(--success-color);
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
    border-color: var(--success-color);
    box-shadow: 0 0 0 3px rgba(121, 195, 71, 0.15);
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: var(--danger-color);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
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
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-primary:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
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

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

.alert ul {
    margin-bottom: 0;
}

.alert li {
    margin-bottom: 4px;
}

.alert li:last-child {
    margin-bottom: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card .card-body {
        padding: 20px 16px;
    }

    .form-section {
        padding: 16px;
    }
}
</style>

@endsection