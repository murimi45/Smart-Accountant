@extends('layouts.app')
@section('main')

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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="white_shd full margin_bottom_30" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
                <div class="full graph_head" style="background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); padding: 25px 30px; border-radius: 12px 12px 0 0;">
                    <h2 style="font-size: 20px; color: #fff; font-weight: 600; margin: 0;">
                        <i class="fa fa-edit me-3"></i>Term Information
                    </h2>
                </div>

                <div class="padding_infor_info" style="padding: 35px 30px;">
                    <form action="{{ route('editterm', $term->id) }}" method="post">
                        @csrf

                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-info-circle me-2 text-primary"></i>Basic Information
                            </h5>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label fw-semibold">Term Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $term->name) }}"
                                           class="form-control @error('name') is-invalid @enderror"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="term_number" class="form-label fw-semibold">Term Number <span class="text-danger">*</span></label>
                                    <input type="number"
                                           id="term_number"
                                           name="term_number"
                                           value="{{ old('term_number', $term->term_number) }}"
                                           class="form-control @error('term_number') is-invalid @enderror"
                                           min="1"
                                           max="4"
                                           required>
                                    @error('term_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label for="academic_year_id" class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                                    <select id="academic_year_id"
                                            name="academic_year_id"
                                            class="form-select @error('academic_year_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- Select Academic Year --</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}"
                                                {{ old('academic_year_id', $term->academic_year_id) == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-clock me-2 text-primary"></i>Term Schedule
                            </h5>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="start_date" class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                    <input type="date"
                                           id="start_date"
                                           name="start_date"
                                           value="{{ old('start_date', $term->start_date ? \Illuminate\Support\Carbon::parse($term->start_date)->format('Y-m-d') : '') }}"
                                           class="form-control @error('start_date') is-invalid @enderror"
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="end_date" class="form-label fw-semibold">End Date</label>
                                    <input type="date"
                                           id="end_date"
                                           name="end_date"
                                           value="{{ old('end_date', $term->end_date ? \Illuminate\Support\Carbon::parse($term->end_date)->format('Y-m-d') : '') }}"
                                           class="form-control @error('end_date') is-invalid @enderror">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-toggle-on me-2 text-primary"></i>Status
                            </h5>

                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       role="switch"
                                       id="active"
                                       name="active"
                                       value="1"
                                       {{ old('active', $term->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    Set as the school's <strong>active term</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Only one term should be active. Checking this will deactivate other terms.
                            </small>
                        </div>

                        <div class="form-actions mt-5 pt-4 text-center" style="border-top: 1px solid #e8eaed;">
                            <button type="submit" class="btn btn-success px-5 py-3 me-3">
                                <i class="fa fa-save me-2"></i>Update Term
                            </button>
                            <a href="{{ route('termlist') }}" class="btn btn-outline-secondary px-5 py-3">
                                <i class="fa fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-section {
    background: #fafbfc;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
}
.form-control:focus,
.form-select:focus {
    border-color: #36a9e2;
    box-shadow: 0 0 0 0.2rem rgba(54, 169, 226, 0.15);
}
</style>

@endsection
