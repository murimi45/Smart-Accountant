@extends('layouts.app')
@section('main')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Edit Term</h4>
                <p class="text-muted mb-0 mt-1">Update term information and settings</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('termlist') }}" class="btn btn-outline-secondary px-4 py-2 fw-bold">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #79c347; border-radius: 8px;">
            <i class="fa fa-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #ff4748; border-radius: 8px;">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Edit Term Form Card --}}
    <div class="row">
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <h5>
                        <i class="fa fa-edit me-2"></i>Term Information
                    </h5>
                </div>

                <div class="form-card-body">
                    <form action="{{ route('editterm', $getRecord->id) }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            {{-- Term Name --}}
                            <div class="col-md-6">
                                <div class="modern-form-group">
                                    <label class="modern-label">
                                        <i class="fa fa-bookmark me-2 text-primary"></i>Term Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fa fa-pencil-alt input-icon"></i>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $getRecord->name) }}" 
                                               class="modern-input @error('name') is-invalid @enderror" 
                                               placeholder="e.g., Term 1, First Term"
                                               required>
                                    </div>
                                    @error('name') 
                                        <small class="text-danger d-block mt-2">
                                            <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                    <small class="form-hint">Enter the term name (e.g., Term 1, First Term)</small>
                                </div>
                            </div>

                            {{-- Term Year --}}
                            <div class="col-md-6">
                                <div class="modern-form-group">
                                    <label class="modern-label">
                                        <i class="fa fa-calendar me-2 text-info"></i>Academic Year
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fa fa-calendar-alt input-icon"></i>
                                        <input type="text" 
                                               id="term_year" 
                                               name="year" 
                                               value="{{ old('year', $getRecord->year) }}" 
                                               class="modern-input @error('year') is-invalid @enderror" 
                                               placeholder="e.g., 2024/2025"
                                               required>
                                    </div>
                                    @error('year') 
                                        <small class="text-danger d-block mt-2">
                                            <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                    <small class="form-hint">Academic year for this term (e.g., 2024/2025)</small>
                                </div>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-6">
                                <div class="modern-form-group">
                                    <label class="modern-label">
                                        <i class="fa fa-play-circle me-2 text-success"></i>Start Date
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fa fa-calendar-day input-icon"></i>
                                        <input type="date" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date', $getRecord->start_date) }}" 
                                               class="modern-input @error('start_date') is-invalid @enderror" 
                                               required>
                                    </div>
                                    @error('start_date') 
                                        <small class="text-danger d-block mt-2">
                                            <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                    <small class="form-hint">When does this term begin?</small>
                                </div>
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-6">
                                <div class="modern-form-group">
                                    <label class="modern-label">
                                        <i class="fa fa-stop-circle me-2 text-danger"></i>End Date
                                        <span class="text-muted">(Optional)</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fa fa-calendar-check input-icon"></i>
                                        <input type="date" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date', $getRecord->end_date) }}" 
                                               class="modern-input @error('end_date') is-invalid @enderror">
                                    </div>
                                    @error('end_date') 
                                        <small class="text-danger d-block mt-2">
                                            <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                    <small class="form-hint">When does this term end?</small>
                                </div>
                            </div>

                            {{-- Active Status --}}
                            
                        </div>

                        {{-- Form Actions --}}
                        <div class="form-actions">
                            <a href="{{ route('termlist') }}" class="btn-form-secondary">
                                <i class="fa fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn-form-primary">
                                <i class="fa fa-save me-2"></i>Update Term
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Form Card Styles */
.form-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 25px;
}

.form-card-header {
    background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%);
    color: #fff;
    padding: 20px 25px;
    border: none;
}

.form-card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #fff;
}

.form-card-body {
    padding: 30px;
}

/* Modern Form Group */
.modern-form-group {
    margin-bottom: 0;
}

.modern-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #000;
    margin-bottom: 10px;
}

.modern-label i {
    font-size: 14px;
}

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
    z-index: 1;
}

.modern-input {
    width: 100%;
    padding: 12px 15px 12px 40px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #1f2937;
    background: #fff;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}

.modern-input:focus {
    outline: none;
    border-color: #36a9e2;
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.modern-input.is-invalid {
    border-color: #ff4748;
}

.modern-input.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(255, 71, 72, 0.1);
}

.form-hint {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #9ca3af;
    font-style: italic;
}

/* Radio Button Group */
.radio-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.radio-option {
    position: relative;
    display: flex;
    align-items: center;
    padding: 12px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
    min-width: 200px;
}

.radio-option:hover {
    border-color: #36a9e2;
    background: #f8f9fa;
}

.radio-option.active {
    border-color: #36a9e2;
    background: rgba(54, 169, 226, 0.05);
}

.radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.radio-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    margin-right: 12px;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.radio-option input[type="radio"]:checked ~ .radio-custom {
    border-color: #36a9e2;
    background: #36a9e2;
}

.radio-option input[type="radio"]:checked ~ .radio-custom::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #fff;
}

.radio-label {
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
}

/* Form Actions */
.form-actions {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f2f3f5;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-form-primary,
.btn-form-secondary {
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.btn-form-primary {
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    color: #fff;
}

.btn-form-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(121, 195, 71, 0.3);
    background: linear-gradient(135deg, #5fa732 0%, #4d8829 100%);
    color: #fff;
}

.btn-form-secondary {
    background: #fff;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.btn-form-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
}

/* Back Button */
.btn-outline-secondary {
    background: #fff;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #374151;
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card-body {
        padding: 20px;
    }
    
    .radio-group {
        flex-direction: column;
    }
    
    .radio-option {
        min-width: 100%;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .btn-form-primary,
    .btn-form-secondary {
        width: 100%;
    }
}
</style>

@endsection