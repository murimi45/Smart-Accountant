@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Add Extra Fee</h4>
                <p class="text-muted mb-0 mt-1">Create a new additional fee category</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('extrafeelist') }}" class="btn btn-secondary px-4 py-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545; border-radius: 8px;">
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-circle me-3 mt-1" style="font-size: 20px;"></i>
                <div class="flex-grow-1">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="row">
        <div class="col-12">
            <div class="white_shd full margin_bottom_30" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
                <div class="full graph_head" style="background: linear-gradient(135deg, #8e68ef 0%, #7344e8 100%); padding: 25px 30px; border-radius: 12px 12px 0 0;">
                    <div class="heading1 margin_0">
                        <h2 style="font-size: 20px; color: #fff; font-weight: 600; margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-receipt me-3" style="font-size: 24px;"></i>
                            Extra Fee Information
                        </h2>
                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">Fill in the details below to create a new extra fee category</p>
                    </div>
                </div>

                <div class="padding_infor_info" style="padding: 35px 30px;">
                    <form action="{{ route('insertextrafee') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        {{-- Basic Information Section --}}
                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-info-circle me-2" style="color: #8e68ef;"></i>
                                Basic Information
                            </h5>

                            <div class="row">
                                {{-- Name --}}
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-tag me-2 text-purple"></i>Fee Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="e.g., Lunch Fee, Transport Fee"
                                           style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Amount --}}
                                <div class="col-md-6 mb-4">
                                    <label for="amount" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-coins me-2 text-warning"></i>Amount (KSh) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group" style="border-radius: 8px;">
                                        <span class="input-group-text" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px 0 0 8px;">
                                            <i class="fa fa-money-bill-wave text-success"></i>
                                        </span>
                                        <input type="number" 
                                               id="amount" 
                                               name="amount" 
                                               value="{{ old('amount') }}" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               placeholder="Enter amount"
                                               step="0.01"
                                               min="0"
                                               style="border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                               required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fa fa-info-circle me-1"></i>Base amount per unit or fixed amount
                                    </small>
                                </div>

                                {{-- Quantity Based --}}
                                <div class="col-md-6 mb-4">
                                    <label for="is_quantity_based" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-sort-numeric-up me-2 text-info"></i>Quantity Based <span class="text-danger">*</span>
                                    </label>
                                    <select id="is_quantity_based" 
                                            name="is_quantity_based" 
                                            class="form-select @error('is_quantity_based') is-invalid @enderror" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            required>
                                        <option value="">Select Option</option>
                                        <option value="0" {{ old('is_quantity_based') == '0' ? 'selected' : '' }}>No (Fixed Amount)</option>
                                        <option value="1" {{ old('is_quantity_based') == '1' ? 'selected' : '' }}>Yes (Multiply by Quantity)</option>
                                    </select>
                                    @error('is_quantity_based')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fa fa-info-circle me-1"></i>Choose "Yes" if fee depends on quantity (e.g., books)
                                    </small>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-toggle-on me-2 text-success"></i>Status <span class="text-danger">*</span>
                                    </label>
                                    <select id="status" 
                                            name="status" 
                                            class="form-select @error('status') is-invalid @enderror" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Term Information Section --}}
                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-calendar me-2" style="color: #8e68ef;"></i>
                                Academic Period
                            </h5>

                            <div class="row">
                                {{-- Term --}}
                                <div class="col-md-12 mb-4">
                                    <label for="term_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-calendar-alt me-2 text-primary"></i>Term <span class="text-danger">*</span>
                                    </label>
                                    <select id="term_id" 
                                            name="term_id" 
                                            class="form-select @error('term_id') is-invalid @enderror" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            required>
                                        <option value="">Select Term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->name }} - {{ $term->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Description Section --}}
                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-file-alt me-2" style="color: #8e68ef;"></i>
                                Additional Details
                            </h5>

                            <div class="row">
                                {{-- Description --}}
                                <div class="col-md-12 mb-4">
                                    <label for="description" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-align-left me-2 text-primary"></i>Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="4"
                                              placeholder="Enter detailed description of the extra fee (e.g., Daily lunch program, School transport service)"
                                              style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="form-actions mt-5 pt-4" style="border-top: 1px solid #e8eaed;">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-success px-5 py-3 me-3" style="border-radius: 8px; font-weight: 600; font-size: 15px; min-width: 180px; background: linear-gradient(135deg, #8e68ef 0%, #7344e8 100%); border: none;">
                                        <i class="fa fa-check-circle me-2"></i>Create Extra Fee
                                    </button>
                                    <a href="{{ route('extrafeelist') }}" class="btn btn-outline-secondary px-5 py-3" style="border-radius: 8px; font-weight: 600; font-size: 15px; min-width: 180px;">
                                        <i class="fa fa-times-circle me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Form Control Focus Effects */
.form-control:focus,
.form-select:focus {
    border-color: #8e68ef;
    box-shadow: 0 0 0 0.2rem rgba(142, 104, 239, 0.15);
}

/* Invalid Feedback Styling */
.invalid-feedback {
    font-size: 13px;
    margin-top: 6px;
}

.is-invalid {
    border-color: #dc3545 !important;
}

/* Input Group Styling */
.input-group-text {
    background: #f8f9fa;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: #8e68ef;
}

.input-group:focus-within .form-control {
    border-color: #8e68ef;
}

/* Button Styles */
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(142, 104, 239, 0.3);
    transition: all 0.3s ease;
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    border: none;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

/* Form Section Styling */
.form-section {
    background: #fafbfc;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
}

/* Custom Colors for Icons */
.text-purple {
    color: #8e68ef !important;
}

/* Placeholder Styling */
.form-control::placeholder,
.form-select::placeholder {
    color: #b0b8c3;
    font-size: 14px;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
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

/* Textarea Styling */
textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .padding_infor_info {
        padding: 25px 20px !important;
    }

    .form-section {
        padding: 20px 15px;
    }

    .btn-success,
    .btn-outline-secondary {
        min-width: 140px !important;
        padding: 12px 20px !important;
        font-size: 14px !important;
    }

    .section-title {
        font-size: 15px !important;
    }

    .full.graph_head {
        padding: 20px 20px !important;
    }

    .full.graph_head h2 {
        font-size: 18px !important;
    }

    .full.graph_head p {
        font-size: 13px !important;
    }
}

@media (max-width: 576px) {
    .btn-success,
    .btn-outline-secondary {
        width: 100%;
        margin-bottom: 10px;
    }

    .btn-success {
        margin-right: 0 !important;
    }
}
</style>

@endsection