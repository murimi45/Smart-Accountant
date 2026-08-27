@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Add Other Income</h4>
                <p class="text-muted mb-0">Record additional income transactions</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('other_incomes.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2 ps-4">
                @foreach($errors->all() as $error)
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
                    <i class="fa fa-wallet me-2"></i>Income Details
                </h5>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('other_incomes.store') }}" method="POST">
                @csrf

                {{-- Income Information Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-info-circle me-2"></i>Income Information
                    </h6>

                    <div class="row g-3">
                        {{-- Income Category --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="income_category_id" class="form-label">
                                <i class="fa fa-tags me-1"></i>Income Category <span class="text-danger">*</span>
                            </label>
                            <select name="income_category_id"
                                    id="income_category_id"
                                    class="form-select @error('income_category_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('income_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('income_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="amount" class="form-label">
                                <i class="fa fa-coins me-1"></i>Amount (KSh) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   step="0.01"
                                   min="0"
                                   placeholder="Enter amount"
                                   value="{{ old('amount') }}"
                                   required>
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="payment_method" class="form-label">
                                <i class="fa fa-credit-card me-1"></i>Payment Method
                            </label>
                            <select name="payment_method"
                                    id="payment_method"
                                    class="form-select @error('payment_method') is-invalid @enderror">
                                <option value="">-- Select Payment Method --</option>
                                <option value="cash" {{ old('payment_method')=='cash' ? 'selected' : '' }}>Cash</option>
                                <option value="mpesa" {{ old('payment_method')=='mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="bank" {{ old('payment_method')=='bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method')=='cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="income_date" class="form-label">
                                <i class="fa fa-calendar me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="income_date"
                                   name="income_date"
                                   class="form-control @error('income_date') is-invalid @enderror"
                                   value="{{ old('income_date', date('Y-m-d')) }}"
                                   required>
                            @error('income_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Academic Period Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-calendar-alt me-2"></i>Academic Period
                    </h6>

                    <div class="row g-3">
                        {{-- Term --}}
                        <div class="col-md-6">
                            <label for="term_id" class="form-label">
                                <i class="fa fa-bookmark me-1"></i>Term <span class="text-danger">*</span>
                            </label>
                            <select name="term_id"
                                    id="term_id"
                                    class="form-select @error('term_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Select Term --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }} ({{ $term->year }})
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Additional Details Section --}}
                <div class="form-section mb-4">
                    <h6 class="section-title">
                        <i class="fa fa-file-alt me-2"></i>Additional Details
                    </h6>

                    <div class="row g-3">
                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="form-label">
                                <i class="fa fa-align-left me-1"></i>Description
                            </label>
                            <textarea id="description"
                                      name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Enter any additional notes or details about this income">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check-circle me-2"></i>Save Income
                    </button>
                    <a href="{{ route('other_incomes.index') }}" class="btn btn-outline-secondary ms-2">
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