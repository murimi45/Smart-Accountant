@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Add New Student</h4>
                <p class="text-muted mb-0">Register a new student in the system</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('listStudents') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
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
                    <i class="fa fa-user-plus me-2"></i>Student Information
                </h5>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('insertStudents') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Personal Information Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-user me-2"></i>Personal Information
                    </h6>

                    <div class="row g-3">
                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="fa fa-user-circle me-1"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Enter full name"
                                   required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Guardian Name --}}
                        <div class="col-md-6">
                            <label for="guardian_name" class="form-label">
                                <i class="fa fa-user-shield me-1"></i>Guardian Name
                            </label>
                            <input type="text"
                                   id="guardian_name"
                                   name="guardian_name"
                                   value="{{ old('guardian_name') }}"
                                   class="form-control @error('guardian_name') is-invalid @enderror"
                                   placeholder="Enter guardian name">
                            @error('guardian_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                <i class="fa fa-phone me-1"></i>Phone Number
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="Enter phone number">
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Admission Number --}}
                        <div class="col-md-6">
                            <label for="admission" class="form-label">
                                <i class="fa fa-hashtag me-1"></i>Admission Number <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="admission"
                                   name="admission"
                                   value="{{ old('admission') }}"
                                   class="form-control @error('admission') is-invalid @enderror"
                                   placeholder="Enter admission number"
                                   required>
                            @error('admission')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6">
                            <label for="gender" class="form-label">
                                <i class="fa fa-venus-mars me-1"></i>Gender <span class="text-danger">*</span>
                            </label>
                            <select id="gender"
                                    name="gender"
                                    class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                <option value="">Select Gender</option>
                                <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Academic Information Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-graduation-cap me-2"></i>Academic Information
                    </h6>

                    <div class="row g-3">
                        {{-- Class --}}
                        <div class="col-md-6">
                            <label for="class_id" class="form-label">
                                <i class="fa fa-school me-1"></i>Class <span class="text-danger">*</span>
                            </label>
                            <select id="class_id"
                                    name="class_id"
                                    class="form-select @error('class_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id') == $class->id ? 'selected' : '' }}>
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
                                    <option value="{{ $term->id }}"
                                        {{ old('term_id', $activeTerm?->id) == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }} - {{ $term->year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            @if($activeTerm ?? null)
                                <small class="text-muted d-block mt-1">
                                    Preselected: <strong>{{ $activeTerm->name }}</strong>@if($activeTerm->year) ({{ $activeTerm->year }})@endif — the school's current term.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check-circle me-2"></i>Submit Application
                    </button>
                    <a href="{{ route('listStudents') }}" class="btn btn-outline-secondary ms-2">
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