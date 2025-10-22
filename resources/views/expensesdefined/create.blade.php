@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">{{ isset($expense) ? 'Edit Expense' : 'Add Expense' }}</h4>
                <p class="text-muted mb-0">{{ isset($expense) ? 'Update expense information' : 'Record a new expense transaction' }}</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-circle me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card form-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa fa-{{ isset($expense) ? 'edit' : 'plus-circle' }} me-2"></i>
                Expense Details
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ isset($expense) ? route('expenses.update', $expense->id) : route('expenses.store') }}" method="POST">
                @csrf
                @if(isset($expense))
                    @method('PUT')
                @endif

                {{-- Expense Information --}}
                <div class="form-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fa fa-info-circle me-2"></i>Expense Information
                    </h6>

                    <div class="row">
                        {{-- Category --}}
                        <div class="col-md-6 mb-3">
                            <label for="expense_category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="expense_category_id" 
                                    id="expense_category_id" 
                                    class="form-select @error('expense_category_id') is-invalid @enderror" 
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ (isset($expense) && $expense->expense_category_id == $cat->id) || old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                Amount (KSh) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-money-bill-wave"></i>
                                </span>
                                <input type="number" 
                                       id="amount" 
                                       name="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ $expense->amount ?? old('amount') }}" 
                                       step="0.01"
                                       min="0"
                                       placeholder="Enter amount"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3"
                                      placeholder="Enter expense description">{{ $expense->description ?? old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="form-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fa fa-credit-card me-2"></i>Payment Details
                    </h6>

                    <div class="row">
                        {{-- Payment Method --}}
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" 
                                    id="payment_method" 
                                    class="form-select @error('payment_method') is-invalid @enderror" 
                                    required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ (isset($expense) && $expense->payment_method=='cash') || old('payment_method')=='cash' ? 'selected' : '' }}>Cash</option>
                                <option value="mpesa" {{ (isset($expense) && $expense->payment_method=='mpesa') || old('payment_method')=='mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="bank" {{ (isset($expense) && $expense->payment_method=='bank') || old('payment_method')=='bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ (isset($expense) && $expense->payment_method=='cheque') || old('payment_method')=='cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Expense Date --}}
                        <div class="col-md-6 mb-3">
                            <label for="expense_date" class="form-label">
                                Expense Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="expense_date" 
                                   name="expense_date" 
                                   class="form-control @error('expense_date') is-invalid @enderror" 
                                   value="{{ optional($expense)->expense_date?->format('Y-m-d') ?? old('expense_date', date('Y-m-d')) }}"
                                   required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Academic Period --}}
                <div class="form-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fa fa-calendar-alt me-2"></i>Academic Period
                    </h6>

                    <div class="row">
                        {{-- Term --}}
                        <div class="col-md-12 mb-3">
                            <label for="term_id" class="form-label">
                                Term <span class="text-danger">*</span>
                            </label>
                            <select name="term_id" 
                                    id="term_id" 
                                    class="form-select @error('term_id') is-invalid @enderror" 
                                    required>
                                <option value="">Select Term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}"
                                        {{ (isset($expense) && $expense->term_id == $term->id) || old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }} ({{ $term->year }})
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions pt-4 mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-{{ isset($expense) ? 'primary' : 'success' }}">
                            <i class="fa fa-{{ isset($expense) ? 'check' : 'save' }} me-2"></i>
                            {{ isset($expense) ? 'Update Expense' : 'Save Expense' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Base Variables */
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

/* Form Card */
.form-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 24px;
}

.form-card .card-header h5 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.form-card .card-body {
    padding: 32px 24px;
}

/* Form Sections */
.form-section {
    margin-bottom: 0;
}

.section-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-700);
    padding-bottom: 12px;
    border-bottom: 1px solid var(--gray-200);
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
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.form-control::placeholder {
    color: var(--gray-500);
    font-size: 14px;
}

/* Input Group */
.input-group-text {
    background-color: var(--gray-50);
    border: 1px solid var(--gray-300);
    border-right: none;
    color: var(--gray-600);
}

.input-group .form-control {
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: var(--primary-color);
    background-color: var(--gray-100);
}

.input-group:focus-within .form-control {
    border-color: var(--primary-color);
}

/* Textarea */
textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

/* Validation States */
.is-invalid {
    border-color: var(--danger-color) !important;
}

.invalid-feedback {
    font-size: 13px;
    color: var(--danger-color);
    margin-top: 4px;
}

/* Buttons */
.btn {
    border-radius: var(--border-radius);
    padding: 8px 20px;
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

.btn-secondary {
    background-color: var(--gray-600);
    border-color: var(--gray-600);
}

.btn-secondary:hover {
    background-color: var(--gray-700);
    border-color: var(--gray-700);
}

/* Form Actions */
.form-actions {
    border-top: 1px solid var(--gray-200);
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 16px;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

.alert ul {
    margin-bottom: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 4px;
}

.alert li:last-child {
    margin-bottom: 0;
}

/* Date Input */
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card .card-body {
        padding: 24px 16px;
    }

    .page-header h4 {
        font-size: 20px;
    }

    .form-actions .d-flex {
        flex-direction: column-reverse;
    }

    .form-actions .btn {
        width: 100%;
        margin-bottom: 8px;
    }

    .form-actions .btn:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 576px) {
    .section-title {
        font-size: 14px;
    }

    .form-card .card-header h5 {
        font-size: 16px;
    }
}
</style>

@endsection