@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Assign Extra Fees</h4>
                <p class="text-muted mb-0 mt-1">Assign additional fees to multiple students</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('listextrafeestudents') }}" class="btn btn-secondary px-4 py-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="row">
        <div class="col-12">
            <div class="white_shd full margin_bottom_30" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
                <div class="full graph_head" style="background: linear-gradient(135deg, #8e68ef 0%, #7344e8 100%); padding: 25px 30px; border-radius: 12px 12px 0 0;">
                    <div class="heading1 margin_0">
                        <h2 style="font-size: 20px; color: #fff; font-weight: 600; margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-users-cog me-3" style="font-size: 24px;"></i>
                            Assign Extra Fees to Students
                        </h2>
                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">Select an extra fee type and choose students to assign</p>
                    </div>
                </div>

                <div class="full px-4 py-4" style="padding: 30px;">
                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('assignextrafeeform') }}">
                        <div class="filter-section mb-4" style="background: #fafbfc; padding: 25px; border-radius: 10px; border: 1px solid #f0f0f0;">
                            <h5 class="mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-filter me-2" style="color: #8e68ef;"></i>
                                Filter Options
                            </h5>
                            <div class="row g-3">
                                {{-- Extra Fee Type --}}
                                <div class="col-md-4">
                                    <label for="extra_fee_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-receipt me-2 text-purple"></i>Extra Fee Type <span class="text-danger">*</span>
                                    </label>
                                    <select name="extra_fee_id" 
                                            id="extra_fee_id" 
                                            class="form-select" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            onchange="this.form.submit()">
                                        <option value="">-- Select Extra Fee --</option>
                                        @foreach($extraFees as $fee)
                                            <option value="{{ $fee->id }}"
                                                data-amount="{{ $fee->amount }}"
                                                data-quantity-based="{{ $fee->is_quantity_based ? '1':'0'}}"
                                                {{ $selectedExtraFee == $fee->id ? 'selected' : '' }}>
                                                {{ $fee->name }} (KES {{ number_format($fee->amount, 2) }}) - {{ $fee->term->name }} {{ $fee->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Class Filter --}}
                                <div class="col-md-3">
                                    <label for="class_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-school me-2 text-success"></i>Class
                                    </label>
                                    <select name="class_id" 
                                            id="class_id" 
                                            class="form-select" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            {{ !$selectedExtraFee ? 'disabled' : '' }} 
                                            onchange="this.form.submit()">
                                        <option value="">-- All Classes --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Search Student --}}
                                <div class="col-md-3">
                                    <label for="search" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-search me-2 text-info"></i>Search Student
                                    </label>
                                    <input type="text" 
                                           name="search" 
                                           id="search" 
                                           value="{{ $searchQuery }}" 
                                           class="form-control" 
                                           placeholder="Name or Admission No."
                                           style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                           {{ !$selectedExtraFee ? 'disabled' : '' }}>
                                </div>

                                {{-- Filter Button --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-cog me-2"></i>Action
                                    </label>
                                    <button type="submit" 
                                            class="btn btn-primary w-100" 
                                            style="border-radius: 8px; padding: 12px;"
                                            {{ !$selectedExtraFee ? 'disabled' : '' }}>
                                        <i class="fa fa-filter me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Students Table --}}
                    @if($students->isNotEmpty())
                        <form action="{{ route('assignextrafee') }}" method="POST">
                            @csrf
                            <input type="hidden" name="extra_fee_id" value="{{ $selectedExtraFee }}">

                            <div class="table-card" style="border-radius: 10px; overflow: hidden; margin-bottom: 20px;">
                                <div class="card-header" style="background: #fafbfc; padding: 15px 20px; border-bottom: 1px solid #e8eaed;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #2c3e50;">
                                            <i class="fa fa-users me-2" style="color: #8e68ef;"></i>
                                            Available Students
                                            <span class="badge bg-primary ms-2" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">
                                                {{ count($students) }}
                                            </span>
                                        </h5>
                                        <div>
                                            <label class="d-flex align-items-center" style="cursor: pointer; font-size: 14px; color: #495057; margin: 0;">
                                                <input type="checkbox" id="select_all" class="me-2" style="cursor: pointer;">
                                                <span>Select All</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table custom-table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"><i class="fa fa-check-square me-2"></i>Select</th>
                                                <th><i class="fa fa-user me-2"></i>Student Name</th>
                                                <th><i class="fa fa-hashtag me-2"></i>Admission No.</th>
                                                <th><i class="fa fa-school me-2"></i>Class</th>
                                                <th class="quantity-col"><i class="fa fa-sort-numeric-up me-2"></i>Quantity</th>
                                                <th class="total-col"><i class="fa fa-dollar-sign me-2"></i>Total (KES)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                @php
                                                    $assigned = $assignedExtraFees->get($student->id);
                                                @endphp
                                                <tr class="student-row">
                                                    <td>
                                                        <input type="hidden" name="students[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                                        <input type="checkbox" 
                                                               class="student-checkbox form-check-input" 
                                                               name="students[{{ $student->id }}][selected]" 
                                                               value="1"
                                                               style="width: 18px; height: 18px; cursor: pointer;"
                                                               {{ $assigned ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                                            </div>
                                                            <strong>{{ $student->name }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark" style="padding: 6px 12px; border-radius: 6px;">
                                                            {{ $student->admission }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $student->class->name }}</td>
                                                    <td class="quantity-col">
                                                        <input type="number" 
                                                               class="form-control quantity-input" 
                                                               name="students[{{ $student->id }}][quantity]" 
                                                               min="1" 
                                                               value="{{ $assigned ? $assigned->quantity : '' }}"
                                                               style="width: 100px; border-radius: 6px;"
                                                               {{ $assigned ? '' : 'disabled' }}>
                                                    </td>
                                                    <td class="total-col">
                                                        <span class="badge student-total" style="background: linear-gradient(135deg, #79c347 0%, #5fa732 100%); color: white; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                                            KES {{ $assigned ? number_format($assigned->amount, 2) : '0.00' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-success px-5 py-3" style="border-radius: 8px; font-weight: 600; font-size: 15px;">
                                    <i class="fa fa-check-circle me-2"></i>Assign Selected Fees
                                </button>
                            </div>
                        </form>
                    @elseif($selectedExtraFee)
                        <div class="text-center py-5" style="color: #9ca3af;">
                            <i class="fa fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="mb-0">No students found for the selected criteria</p>
                            <small>Try adjusting your filters</small>
                        </div>
                    @else
                        <div class="text-center py-5" style="color: #9ca3af;">
                            <i class="fa fa-hand-pointer fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="mb-0">Please select an extra fee type to begin</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Form Controls */
.form-select:focus,
.form-control:focus {
    border-color: #8e68ef;
    box-shadow: 0 0 0 0.2rem rgba(142, 104, 239, 0.15);
}

.form-select:disabled,
.form-control:disabled {
    background-color: #f5f7fa;
    cursor: not-allowed;
}

/* Checkbox Styling */
.form-check-input:checked {
    background-color: #8e68ef;
    border-color: #8e68ef;
}

#select_all {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(54, 169, 226, 0.3);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-success {
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(121, 195, 71, 0.3);
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

/* Text Purple */
.text-purple {
    color: #8e68ef !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .user-avatar {
        width: 28px !important;
        height: 28px !important;
        font-size: 11px !important;
    }
    
    .table {
        font-size: 13px;
    }

    .badge {
        font-size: 11px !important;
        padding: 4px 8px !important;
    }

    .quantity-input {
        width: 80px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const extraFeeSelect = document.getElementById('extra_fee_id');
    const studentRows = document.querySelectorAll('.student-row');
    const selectAllCheckbox = document.getElementById('select_all');

    function calculateTotal(row, amount, quantityBased) {
        const quantityInput = row.querySelector('.quantity-input');
        const totalField = row.querySelector('.student-total');
        let quantity = quantityBased ? parseFloat(quantityInput.value) || 0 : 1;
        const total = quantity * amount;
        totalField.textContent = `KES ${total.toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    function updateUIBasedOnFee() {
        const selectedOption = extraFeeSelect.options[extraFeeSelect.selectedIndex];
        const amount = parseFloat(selectedOption.dataset.amount);
        const quantityBased = selectedOption.dataset.quantityBased === '1';

        // Show or hide quantity column
        document.querySelectorAll('.quantity-col').forEach(cell => {
            cell.style.display = quantityBased ? 'table-cell' : 'none';
        });

        studentRows.forEach(row => {
            const checkbox = row.querySelector('.student-checkbox');
            const quantityInput = row.querySelector('.quantity-input');

            if (!checkbox.checked && !quantityInput.value) {
                quantityInput.disabled = true;
            }

            if (!quantityBased) {
                quantityInput.disabled = true;
                quantityInput.value = 1;
            }

            calculateTotal(row, amount, quantityBased);
        });
    }

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            studentRows.forEach(row => {
                const checkbox = row.querySelector('.student-checkbox');
                const quantityInput = row.querySelector('.quantity-input');
                
                checkbox.checked = selectAllCheckbox.checked;
                
                if (checkbox.checked) {
                    quantityInput.disabled = false;
                    if (!quantityInput.value) quantityInput.value = 1;
                } else {
                    quantityInput.disabled = true;
                    quantityInput.value = '';
                }
                
                const selectedOption = extraFeeSelect.options[extraFeeSelect.selectedIndex];
                const amount = parseFloat(selectedOption.dataset.amount);
                const quantityBased = selectedOption.dataset.quantityBased === '1';
                calculateTotal(row, amount, quantityBased);
            });
        });
    }

    // Enable/disable quantity when checkbox changes
    studentRows.forEach(row => {
        const checkbox = row.querySelector('.student-checkbox');
        const quantityInput = row.querySelector('.quantity-input');
        
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                quantityInput.disabled = false;
                if (!quantityInput.value) quantityInput.value = 1;
            } else {
                quantityInput.disabled = true;
                quantityInput.value = '';
            }
            const selectedOption = extraFeeSelect.options[extraFeeSelect.selectedIndex];
            const amount = parseFloat(selectedOption.dataset.amount);
            const quantityBased = selectedOption.dataset.quantityBased === '1';
            calculateTotal(row, amount, quantityBased);
        });

        // Recalculate total on quantity change
        quantityInput.addEventListener('input', () => {
            const selectedOption = extraFeeSelect.options[extraFeeSelect.selectedIndex];
            const amount = parseFloat(selectedOption.dataset.amount);
            const quantityBased = selectedOption.dataset.quantityBased === '1';
            calculateTotal(row, amount, quantityBased);
        });
    });

    updateUIBasedOnFee();
});
</script>
@endsection