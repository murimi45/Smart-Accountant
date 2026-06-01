

<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <h4 class="mb-1">Welcome back!</h4>
        <p class="text-muted mb-0">Here's your School Accounts overview</p>
    </div>

    
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-eye me-1"></i>View Type</label>
                        <select name="view" class="form-select" onchange="this.form.submit()">
                            <option value="term" <?php echo e($viewType === 'term' ? 'selected' : ''); ?>>Term View</option>
                            <option value="annual" <?php echo e($viewType === 'annual' ? 'selected' : ''); ?>>Annual View</option>
                        </select>
                    </div>

                    <?php if($viewType === 'term'): ?>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Select Term</label>
                        <select name="term_id" class="form-select" onchange="this.form.submit()">
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" 
                                    <?php echo e(isset($selectedTerm) && $selectedTerm->id == $term->id ? 'selected' : ''); ?>>
                                    <?php echo e($term->name); ?> (<?php echo e($term->year); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar-alt me-1"></i>Select Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
    <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $academicYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($academicYear->id); ?>" 
            <?php echo e(isset($selectedYear) && $selectedYear->id == $academicYear->id ? 'selected' : ''); ?>>
            <?php echo e($academicYear->name); ?>

        </option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #e0f2fe;">
                    <i class="fa fa-credit-card" style="color: #36a9e2;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($totalFeesBilled, 2)); ?></div>
                    <div class="summary-label">Total Fees Billed</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #e8f5e0;">
                    <i class="fa fa-money-bill-wave" style="color: #79c347;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($totalFeesCollected, 2)); ?></div>
                    <div class="summary-label">Fees Collected</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #fee2e2;">
                    <i class="fa fa-balance-scale" style="color: #ef4444;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($outstandingBalances, 2)); ?></div>
                    <div class="summary-label">Outstanding Balance</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #fef3c7;">
                    <i class="fa fa-chart-line" style="color: #f59e0b;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($netPosition, 2)); ?></div>
                    <div class="summary-label">Net Position</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="summary-card-secondary">
                <div class="summary-icon-secondary" style="background-color: #d1fae5;">
                    <i class="fa fa-plus-circle" style="color: #10b981;"></i>
                </div>
                <div class="summary-content-secondary">
                    <div class="summary-value-secondary">KSh <?php echo e(number_format($otherIncome, 2)); ?></div>
                    <div class="summary-label-secondary">Other Income</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card-secondary">
                <div class="summary-icon-secondary" style="background-color: #fee2e2;">
                    <i class="fa fa-minus-circle" style="color: #ef4444;"></i>
                </div>
                <div class="summary-content-secondary">
                    <div class="summary-value-secondary">KSh <?php echo e(number_format($totalExpenses, 2)); ?></div>
                    <div class="summary-label-secondary">Total Expenses</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        
        <div class="col-xl-8 mb-4">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa fa-receipt me-2"></i>Recent Payments
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="card-body p-0">
    <div class="table-responsive">
        <table class="table dashboard-table mb-0">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th class="d-none d-lg-table-cell">Class</th>
                    <th>Amount</th>
                    <th class="d-none d-md-table-cell">Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
               <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
    <td>
        <span class="student-id">
            <?php echo e($payment->student?->student_id ?? 'N/A'); ?>

        </span>
    </td>
    <td>
        <?php echo e($payment->student?->name ?? 'Unknown Student'); ?>

    </td>
    <td class="d-none d-lg-table-cell">
        <span class="badge-class">
            <?php echo e($payment->student?->class_name ?? 'N/A'); ?>

        </span>
    </td>
    <td>
        <span class="amount-text">KSh <?php echo e(number_format($payment->amount)); ?></span>
    </td>
    <td class="d-none d-md-table-cell">
        <?php echo e($payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A'); ?>

    </td>
    <td>
        <span class="badge 
            <?php echo e($payment->status === 'Paid' ? 'badge-success' : ($payment->status === 'Pending' ? 'badge-warning' : 'badge-danger')); ?>">
            <?php echo e($payment->status ?? 'Unknown'); ?>

        </span>
    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr>
    <td colspan="7" class="text-center">No recent payments found.</td>
</tr>
<?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

    
</div>

            </div>
        </div>

        
        <div class="col-xl-4 mb-4">
            
            <div class="card quick-actions-card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="fa fa-bolt me-2"></i>Quick Actions
                    </h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-quick">
                            <i class="fa fa-plus-circle me-2"></i>Record New Payment
                        </button>
                        <button class="btn btn-success btn-quick">
                            <i class="fa fa-file-invoice me-2"></i>Generate Invoice
                        </button>
                        <button class="btn btn-warning btn-quick">
                            <i class="fa fa-user-plus me-2"></i>Add Student
                        </button>
                        <button class="btn btn-secondary btn-quick">
                            <i class="fa fa-download me-2"></i>Export Report
                        </button>
                    </div>
                </div>
            </div>

            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard-notifications', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-586184870-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>
    </div>

    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="fa fa-pie-chart me-2"></i>Fees vs Collections
                    </h5>
                    <div class="chart-container">
                        <canvas id="feesPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="fa fa-shopping-cart me-2"></i>Expenses Breakdown
                    </h5>
                    <div class="chart-container">
                        <canvas id="expensesPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="fa fa-chart-line me-2"></i>Net Position Over Time
                    </h5>
                    <div class="chart-container">
                        <canvas id="netLineChart"></canvas>
                    </div>
                </div>
            </div>
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
    --warning-color: #f59e0b;
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

/* Cards */
.filter-card,
.table-card,
.quick-actions-card,
.chart-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-card .card-body,
.quick-actions-card .card-body,
.chart-card .card-body {
    padding: 20px;
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
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

/* Summary Cards */
.summary-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.summary-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.summary-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-icon i {
    font-size: 24px;
}

.summary-content {
    flex: 1;
    min-width: 0;
}

.summary-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.summary-label {
    font-size: 13px;
    color: var(--gray-500);
    font-weight: 500;
}

/* Secondary Summary Cards */
.summary-card-secondary {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
}

.summary-card-secondary:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.summary-icon-secondary {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-icon-secondary i {
    font-size: 28px;
}

.summary-content-secondary {
    flex: 1;
}

.summary-value-secondary {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.summary-label-secondary {
    font-size: 14px;
    color: var(--gray-500);
    font-weight: 500;
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

.btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
    border-color: #d97706;
    color: white;
}

.btn-secondary {
    background-color: var(--gray-600);
    border-color: var(--gray-600);
}

.btn-secondary:hover {
    background-color: var(--gray-700);
    border-color: var(--gray-700);
}

.btn-light {
    background-color: var(--gray-100);
    border-color: var(--gray-200);
    color: var(--gray-700);
}

.btn-light:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
}

.btn-quick {
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 500;
}

/* Table */
.dashboard-table {
    font-size: 14px;
}

.dashboard-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.dashboard-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.dashboard-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.dashboard-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Student ID */
.student-id {
    color: var(--gray-600);
    font-weight: 500;
    font-size: 13px;
}

/* Badge Class */
.badge-class {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Amount */
.amount-text {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 14px;
}

/* Status Badges */
.badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
}

.badge-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.badge-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Quick Actions */
.quick-actions-card h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

/* Charts */
.chart-card h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.chart-container {
    position: relative;
    width: 100%;
    min-height: 300px;
}

/* Responsive Design */
@media (max-width: 992px) {
    .summary-value {
        font-size: 16px;
    }

    .summary-value-secondary {
        font-size: 20px;
    }
}

@media (max-width: 768px) {
    .summary-card {
        padding: 16px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
    }

    .summary-icon i {
        font-size: 20px;
    }

    .summary-value {
        font-size: 15px;
    }

    .summary-label {
        font-size: 12px;
    }

    .summary-card-secondary {
        padding: 18px;
    }

    .summary-icon-secondary {
        width: 56px;
        height: 56px;
    }

    .summary-icon-secondary i {
        font-size: 24px;
    }

    .summary-value-secondary {
        font-size: 18px;
    }

    .dashboard-table {
        font-size: 13px;
    }

    .dashboard-table thead th,
    .dashboard-table tbody td {
        padding: 10px;
    }

    .chart-container {
        min-height: 250px;
    }
}

@media (max-width: 576px) {
    .summary-card {
        padding: 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
    }

    .summary-icon i {
        font-size: 18px;
    }

    .summary-value {
        font-size: 14px;
    }

    .action-buttons .btn-sm {
        padding: 6px 10px;
    }
}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fees vs Collections
    const feesCtx = document.getElementById('feesPieChart').getContext('2d');
    new Chart(feesCtx, {
        type: 'pie',
        data: {
            labels: ['Collected', 'Outstanding'],
            datasets: [{
                data: [<?php echo e($totalFeesCollected); ?>, <?php echo e($outstandingBalances); ?>],
                backgroundColor: ['#79c347', '#ef4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Expenses Breakdown
    const expensesCtx = document.getElementById('expensesPieChart').getContext('2d');
    new Chart(expensesCtx, {
        type: 'pie',
        data: {
            labels: [
                <?php $__currentLoopData = $expensesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    '<?php echo e($exp->category->name); ?>',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [{
                data: [
                    <?php $__currentLoopData = $expensesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($exp->total); ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                backgroundColor: ['#36a9e2','#8e68ef','#fabb3d','#ef4444','#79c347','#f59e0b'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Net Position Over Time
    const netCtx = document.getElementById('netLineChart').getContext('2d');
    new Chart(netCtx, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Net Position',
                data: [<?php echo e(implode(',', $monthlyNet)); ?>],
                borderColor: '#36a9e2',
                backgroundColor: 'rgba(54, 169, 226, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#36a9e2',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/dashboard.blade.php ENDPATH**/ ?>