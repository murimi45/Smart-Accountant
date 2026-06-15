

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Assign Extra Fees</h4>
                <p class="text-muted mb-0 mt-1">Assign additional fees to multiple students</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('listextrafeestudents')); ?>" class="btn btn-secondary px-4 py-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    
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
                    
                    <form method="GET" action="<?php echo e(route('assignextrafeeform')); ?>">
                        <div class="filter-section mb-4" style="background: #fafbfc; padding: 25px; border-radius: 10px; border: 1px solid #f0f0f0;">
                            <h5 class="mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-filter me-2" style="color: #8e68ef;"></i>
                                Filter Options
                            </h5>
                            <div class="row g-3">
                                
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
                                        <?php $__currentLoopData = $extraFees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($fee->id); ?>"
                                                data-amount="<?php echo e($fee->amount); ?>"
                                                data-quantity-based="<?php echo e($fee->is_quantity_based ? '1':'0'); ?>"
                                                <?php echo e($selectedExtraFee == $fee->id ? 'selected' : ''); ?>>
                                                <?php echo e($fee->name); ?> (KES <?php echo e(number_format($fee->amount, 2)); ?>) - <?php echo e($fee->term->name); ?> <?php echo e($fee->year); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div class="col-md-3">
                                    <label for="class_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-school me-2 text-success"></i>Class
                                    </label>
                                    <select name="class_id" 
                                            id="class_id" 
                                            class="form-select" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            <?php echo e(!$selectedExtraFee ? 'disabled' : ''); ?> 
                                            onchange="this.form.submit()">
                                        <option value="">-- All Classes --</option>
                                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($class->id); ?>" <?php echo e($selectedClass == $class->id ? 'selected' : ''); ?>>
                                                <?php echo e($class->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div class="col-md-3">
                                    <label for="search" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-search me-2 text-info"></i>Search Student
                                    </label>
                                    <input type="text" 
                                           name="search" 
                                           id="search" 
                                           value="<?php echo e($searchQuery); ?>" 
                                           class="form-control" 
                                           placeholder="Name or Admission No."
                                           style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                           <?php echo e(!$selectedExtraFee ? 'disabled' : ''); ?>>
                                </div>

                                
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-cog me-2"></i>Action
                                    </label>
                                    <button type="submit" 
                                            class="btn btn-primary w-100" 
                                            style="border-radius: 8px; padding: 12px;"
                                            <?php echo e(!$selectedExtraFee ? 'disabled' : ''); ?>>
                                        <i class="fa fa-filter me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    
                    <?php if($students->isNotEmpty()): ?>
                        <form action="<?php echo e(route('assignextrafee')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="extra_fee_id" value="<?php echo e($selectedExtraFee); ?>">

                            <div class="table-card" style="border-radius: 10px; overflow: hidden; margin-bottom: 20px;">
                                <div class="card-header" style="background: #fafbfc; padding: 15px 20px; border-bottom: 1px solid #e8eaed;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #2c3e50;">
                                            <i class="fa fa-users me-2" style="color: #8e68ef;"></i>
                                            Available Students
                                            <span class="badge bg-primary ms-2" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">
                                                <?php echo e(count($students)); ?>

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
                                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $assigned = $assignedExtraFees->get($student->id);
                                                    $enrollment = $student->enrollments->first();
                                                    $className = $enrollment?->schoolClass?->name ?? '—';
                                                ?>
                                                <tr class="student-row">
                                                    <td>
                                                        <input type="hidden" name="students[<?php echo e($student->id); ?>][student_id]" value="<?php echo e($student->id); ?>">
                                                        <input type="checkbox" 
                                                               class="student-checkbox form-check-input" 
                                                               name="students[<?php echo e($student->id); ?>][selected]" 
                                                               value="1"
                                                               style="width: 18px; height: 18px; cursor: pointer;"
                                                               <?php echo e($assigned ? 'checked' : ''); ?>>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                                                                <?php echo e(strtoupper(substr($student->full_name, 0, 1))); ?>

                                                            </div>
                                                            <strong><?php echo e($student->full_name); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark" style="padding: 6px 12px; border-radius: 6px;">
                                                            <?php echo e($student->admission); ?>

                                                        </span>
                                                    </td>
                                                    <td><?php echo e($className); ?></td>
                                                    <td class="quantity-col">
                                                        <input type="number" 
                                                               class="form-control quantity-input" 
                                                               name="students[<?php echo e($student->id); ?>][quantity]" 
                                                               min="1" 
                                                               value="<?php echo e($assigned ? $assigned->quantity : ''); ?>"
                                                               style="width: 100px; border-radius: 6px;"
                                                               <?php echo e($assigned ? '' : 'disabled'); ?>>
                                                    </td>
                                                    <td class="total-col">
                                                        <span class="badge student-total" style="background: linear-gradient(135deg, #79c347 0%, #5fa732 100%); color: white; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                                            KES <?php echo e($assigned ? number_format($assigned->amount, 2) : '0.00'); ?>

                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <?php elseif($selectedExtraFee): ?>
                        <div class="text-center py-5" style="color: #9ca3af;">
                            <i class="fa fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="mb-0">No students found for the selected criteria</p>
                            <small>Try adjusting your filters</small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5" style="color: #9ca3af;">
                            <i class="fa fa-hand-pointer fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="mb-0">Please select an extra fee type to begin</p>
                        </div>
                    <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/extrafee/assignextrafee.blade.php ENDPATH**/ ?>