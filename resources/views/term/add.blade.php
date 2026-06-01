@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Add New Term</h4>
                <p class="text-muted mb-0 mt-1">Create a new academic term in the system</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('termlist') }}" class="btn btn-secondary px-4 py-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #28a745; border-radius: 8px;">
            <i class="fa fa-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545; border-radius: 8px;">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="row">
        <div class="col-12">
            <div class="white_shd full margin_bottom_30" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
                <div class="full graph_head" style="background: linear-gradient(135deg, #fabb3d 0%, #f9a825 100%); padding: 25px 30px; border-radius: 12px 12px 0 0;">
                    <div class="heading1 margin_0">
                        <h2 style="font-size: 20px; color: #fff; font-weight: 600; margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-calendar-plus me-3" style="font-size: 24px;"></i>
                            Term Information
                        </h2>
                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">Fill in the details below to create a new academic term</p>
                    </div>
                </div>

                <div class="padding_infor_info" style="padding: 35px 30px;">
                    <form action="{{ route('insertterm') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        {{-- Basic Information Section --}}
                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-info-circle me-2" style="color: #fabb3d;"></i>
                                Basic Information
                            </h5>

                            <div class="row">
    {{-- Term Name --}}
    <div class="col-md-6 mb-4">
        <label for="name" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
            <i class="fa fa-bookmark me-2 text-warning"></i>Term Name <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               value="{{ old('name', $term->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="e.g., Term 1, First Semester"
               style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
   
    <div class="col-md-6 mb-4">
        <label for="term_number" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
            <i class="fa fa-list-ol me-2 text-info"></i>Term Number <span class="text-danger">*</span>
        </label>
        <input type="number"
               id="term_number"
               name="term_number"
               value="{{ old('term_number', $term->term_number ?? '') }}"
               class="form-control @error('term_number') is-invalid @enderror"
               placeholder="e.g., 1 for Term 1, 2 for Term 2"
               style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
               required>
        @error('term_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Academic Year --}}
    <div class="col-md-12 mb-4">
        <label for="academic_year_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
            <i class="fa fa-calendar me-2 text-primary"></i>Academic Year <span class="text-danger">*</span>
        </label>
        <select id="academic_year_id"
                name="academic_year_id"
                class="form-select @error('academic_year_id') is-invalid @enderror"
                style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                required>
            <option value="">-- Select Academic Year --</option>
            @foreach($academicYears as $year)
                <option value="{{ $year->id }}"
                    {{ old('academic_year_id', $term->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
                    {{ $year->name }}
                </option>
            @endforeach
        </select>
        @error('academic_year_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    
</div>



{{-- Schedule Section --}}
<div class="form-section mb-4">
    <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
        <i class="fa fa-clock me-2" style="color: #fabb3d;"></i>
        Term Schedule
    </h5>

    <div class="row">
        {{-- Start Date --}}
        <div class="col-md-6 mb-4">
            <label for="start_date" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                <i class="fa fa-play-circle me-2 text-success"></i>Start Date <span class="text-danger">*</span>
            </label>
            <input type="date"
                   id="start_date"
                   name="start_date"
                   value="{{ old('start_date', isset($term) ? $term->start_date : '') }}"
                   class="form-control @error('start_date') is-invalid @enderror"
                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                   required>
            @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- End Date --}}
        <div class="col-md-6 mb-4">
            <label for="end_date" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                <i class="fa fa-stop-circle me-2 text-danger"></i>End Date
            </label>
            <input type="date"
                   id="end_date"
                   name="end_date"
                   value="{{ old('end_date', isset($term) ? $term->end_date : '') }}"
                   class="form-control @error('end_date') is-invalid @enderror"
                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;">
            @error('end_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

                        {{-- Status Section --}}
                        <!-- <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-toggle-on me-2" style="color: #fabb3d;"></i>
                                Term Status
                            </h5> -->

                            <!-- <div class="row">
                                {{-- Status --}}
                                <div class="col-md-6 mb-4">
                                    <label for="active" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-power-off me-2 text-info"></i>Status <span class="text-danger">*</span>
                                    </label>
                                    <select id="active" 
                                            name="active" 
                                            class="form-select @error('active') is-invalid @enderror" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            required>
                                        <option value="">-- Select Status --</option>
                                        <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fa fa-info-circle me-1"></i>Active terms will be available for student enrollment
                                    </small>
                                </div>
                            </div> -->
                        </div>

                        {{-- Form Actions --}}
                        <div class="form-actions mt-5 pt-4" style="border-top: 1px solid #e8eaed;">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-success px-5 py-3 me-3" style="border-radius: 8px; font-weight: 600; font-size: 15px; min-width: 180px; background: linear-gradient(135deg, #fabb3d 0%, #f9a825 100%); border: none;">
                                        <i class="fa fa-check-circle me-2"></i>Create Term
                                    </button>
                                    <a href="{{ route('termlist') }}" class="btn btn-outline-secondary px-5 py-3" style="border-radius: 8px; font-weight: 600; font-size: 15px; min-width: 180px;">
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
    border-color: #fabb3d;
    box-shadow: 0 0 0 0.2rem rgba(250, 187, 61, 0.15);
}

/* Invalid Feedback Styling */
.invalid-feedback {
    font-size: 13px;
    margin-top: 6px;
}

.is-invalid {
    border-color: #dc3545 !important;
}

/* Button Styles */
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(250, 187, 61, 0.3);
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

/* Placeholder Styling */
.form-control::placeholder {
    color: #b0b8c3;
    font-size: 14px;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
}

/* Date Input Styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
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