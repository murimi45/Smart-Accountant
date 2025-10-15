@extends('layouts.app')

@section('main')

<div class="main-wrapper">
    {{-- Welcome Header --}}
    <div class="page_title mb-4">
        <h4 class="font-weight-bold mb-1">Welcome back!</h4>
        <p class="text-muted mb-0">Here's your School Accounts overview</p>
    </div>


    <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2">
            
            <!-- View Type -->
            <select name="view" class="form-select" onchange="this.form.submit()">
                <option value="term" {{ $viewType === 'term' ? 'selected' : '' }}>Term View</option>
                <option value="annual" {{ $viewType === 'annual' ? 'selected' : '' }}>Annual View</option>
            </select>

            @if($viewType === 'term')
                <!-- Term Dropdown -->
                <select name="term_id" class="form-select" onchange="this.form.submit()">
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" 
                            {{ isset($selectedTerm) && $selectedTerm->id == $term->id ? 'selected' : '' }}>
                            {{ $term->name }} ({{ $term->year }})
                        </option>
                    @endforeach
                </select>
            @else
                <!-- Year Dropdown -->
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $year)
                        <option value="{{ $year }}" 
                            {{ isset($selectedYear) && $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            @endif

        </form>
    </div>
</div>


   



    {{-- Financial Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <i class="fa fa-credit-card"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalFeesBilled) }}</h3>
                    <p>Total Fees Billed</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-lg-3">
            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalFeesCollected) }}</h3>
                    <p>Fees Collected</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-lg-3">
            <div class="stat-card stat-orange">
                <div class="stat-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($outstandingBalances) }}</h3>
                    <p>Outstanding Balance</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-lg-3">
            <div class="stat-card stat-yellow">
                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($netPosition) }}</h3>
                    <p>Net Position</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="stat-card-secondary stat-teal">
                <div class="stat-icon-secondary">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <div class="stat-content-secondary">
                    <h4>{{ number_format($otherIncome) }}</h4>
                    <p>Other Income</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="stat-card-secondary stat-red-secondary">
                <div class="stat-icon-secondary">
                    <i class="fa fa-minus-circle"></i>
                </div>
                <div class="stat-content-secondary">
                    <h4>{{ number_format($totalExpenses) }}</h4>
                    <p>Total Expenses</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Row --}}
    <div class="row g-3 mb-4">
        {{-- Recent Payments Table --}}
        <div class="col-12 col-xl-8">
            <div class="table-card">
                <div class="card-header">
                    <h5><i class="fa fa-receipt me-2"></i> Recent Payments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th class="d-none d-lg-table-cell">Class</th>
                                    <th>Amount</th>
                                    <th class="d-none d-md-table-cell">Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong class="text-nowrap">STU-001</strong></td>
                                    <td class="text-nowrap">John Kamau</td>
                                    <td class="d-none d-lg-table-cell">Form 4A</td>
                                    <td><strong class="text-nowrap">45,000</strong></td>
                                    <td class="d-none d-md-table-cell text-nowrap">Oct 05, 2025</td>
                                    <td><span class="badge-status badge-paid">Paid</span></td>
                                    <td class="text-center">
                                        <button class="btn-action" title="View"><i class="fa fa-eye"></i></button>
                                        <button class="btn-action d-none d-sm-inline-block" title="Print"><i class="fa fa-print"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="text-nowrap">STU-087</strong></td>
                                    <td class="text-nowrap">Mary Wanjiku</td>
                                    <td class="d-none d-lg-table-cell">Form 3B</td>
                                    <td><strong class="text-nowrap">38,500</strong></td>
                                    <td class="d-none d-md-table-cell text-nowrap">Oct 04, 2025</td>
                                    <td><span class="badge-status badge-paid">Paid</span></td>
                                    <td class="text-center">
                                        <button class="btn-action" title="View"><i class="fa fa-eye"></i></button>
                                        <button class="btn-action d-none d-sm-inline-block" title="Print"><i class="fa fa-print"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="text-nowrap">STU-143</strong></td>
                                    <td class="text-nowrap">Peter Omondi</td>
                                    <td class="d-none d-lg-table-cell">Form 2C</td>
                                    <td><strong class="text-nowrap">32,000</strong></td>
                                    <td class="d-none d-md-table-cell text-nowrap">Oct 03, 2025</td>
                                    <td><span class="badge-status badge-pending">Pending</span></td>
                                    <td class="text-center">
                                        <button class="btn-action" title="View"><i class="fa fa-eye"></i></button>
                                        <button class="btn-action d-none d-sm-inline-block" title="Print"><i class="fa fa-print"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="text-nowrap">STU-256</strong></td>
                                    <td class="text-nowrap">Grace Akinyi</td>
                                    <td class="d-none d-lg-table-cell">Form 1A</td>
                                    <td><strong class="text-nowrap">28,000</strong></td>
                                    <td class="d-none d-md-table-cell text-nowrap">Oct 02, 2025</td>
                                    <td><span class="badge-status badge-paid">Paid</span></td>
                                    <td class="text-center">
                                        <button class="btn-action" title="View"><i class="fa fa-eye"></i></button>
                                        <button class="btn-action d-none d-sm-inline-block" title="Print"><i class="fa fa-print"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="text-nowrap">STU-189</strong></td>
                                    <td class="text-nowrap">David Mwangi</td>
                                    <td class="d-none d-lg-table-cell">Form 4B</td>
                                    <td><strong class="text-nowrap">45,000</strong></td>
                                    <td class="d-none d-md-table-cell text-nowrap">Oct 01, 2025</td>
                                    <td><span class="badge-status badge-overdue">Overdue</span></td>
                                    <td class="text-center">
                                        <button class="btn-action" title="View"><i class="fa fa-eye"></i></button>
                                        <button class="btn-action d-none d-sm-inline-block" title="Print"><i class="fa fa-print"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong class="text-nowrap">STU-312</strong></td>
                                    <td class="text-nowrap">Sarah Njeri</td>
                                    <td class="d-none d-lg-table-cell">Form 3A</td>
                                    <td><strong class="text-nowrap">38,500</strong></td>
                                    <td class="d-none d-md-table-cell text-nowrap">Sep 30, 2025</td>
                                    <td><span class="badge-status badge-paid">Paid</span></td>
                                    <td class="text-center">
                                        <button class="btn-action" title="View"><i class="fa fa-eye"></i></button>
                                        <button class="btn-action d-none d-sm-inline-block" title="Print"><i class="fa fa-print"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-xl-4">
            {{-- Quick Actions --}}
            <div class="quick-actions-card mb-3">
                <h5><i class="fa fa-bolt me-2"></i> Quick Actions</h5>
                <button class="btn-quick-action btn-quick-primary">
                    <i class="fa fa-plus-circle"></i> Record New Payment
                </button>
                <button class="btn-quick-action btn-quick-success">
                    <i class="fa fa-file-invoice"></i> Generate Invoice
                </button>
                <button class="btn-quick-action btn-quick-warning">
                    <i class="fa fa-user-plus"></i> Add Student
                </button>
                <button class="btn-quick-action btn-quick-purple">
                    <i class="fa fa-download"></i> Export Report
                </button>
            </div>

            {{-- Notifications --}}
            <div class="notifications-card">
                <h5><i class="fa fa-bell me-2"></i> Recent Notifications</h5>
                
                <div class="notification-item">
                    <div class="notification-icon warning">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="notification-content">
                        <h6>Payment Overdue Alert</h6>
                        <p>23 students have overdue fee payments for Term 1</p>
                        <small><i class="fa fa-clock"></i> 2 hours ago</small>
                    </div>
                </div>

                <div class="notification-item">
                    <div class="notification-icon success">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="notification-content">
                        <h6>Bulk Payment Received</h6>
                        <p>Form 4A class fees received - KES 850,000</p>
                        <small><i class="fa fa-clock"></i> 5 hours ago</small>
                    </div>
                </div>

                <div class="notification-item">
                    <div class="notification-icon info">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div class="notification-content">
                        <h6>Fee Structure Updated</h6>
                        <p>New fee schedule approved for Term 2, 2025</p>
                        <small><i class="fa fa-clock"></i> 1 day ago</small>
                    </div>
                </div>

                <div class="notification-item">
                    <div class="notification-icon warning">
                        <i class="fa fa-bell"></i>
                    </div>
                    <div class="notification-content">
                        <h6>Reminder: End of Month</h6>
                        <p>Monthly financial report due in 3 days</p>
                        <small><i class="fa fa-clock"></i> 1 day ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <h5><i class="fa fa-pie-chart me-2"></i> Fees vs Collections</h5>
                <div class="chart-container">
                    <canvas id="feesPieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <h5><i class="fa fa-shopping-cart me-2"></i> Expenses Breakdown</h5>
                <div class="chart-container">
                    <canvas id="expensesPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <h5><i class="fa fa-line-chart me-2"></i> Net Position Over Time</h5>
                <div class="chart-container">
                    <canvas id="netLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional Responsive Fixes */
.main-wrapper {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    overflow-x: hidden;
}

/* Ensure cards don't overflow */
.stat-card,
.stat-card-secondary,
.table-card,
.chart-card,
.quick-actions-card,
.notifications-card {
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

/* Chart Container */
.chart-container {
    position: relative;
    width: 100%;
    height: auto;
    min-height: 300px;
    padding: 10px;
}

/* Table Responsive Improvements */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.custom-table {
    min-width: 600px;
    width: 100%;
}

/* Text No Wrap */
.text-nowrap {
    white-space: nowrap;
}

/* Mobile Stat Cards */
@media (max-width: 576px) {
    .main-wrapper {
        padding: 15px 10px;
    }

    .stat-card {
        padding: 15px;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .stat-content h3 {
        font-size: 18px;
    }

    .stat-content p {
        font-size: 11px;
    }

    .stat-card-secondary {
        padding: 18px;
    }

    .stat-icon-secondary {
        width: 50px;
        height: 50px;
        font-size: 22px;
    }

    .stat-content-secondary h4 {
        font-size: 22px;
    }

    .stat-content-secondary p {
        font-size: 13px;
    }

    /* Quick Actions on Mobile */
    .btn-quick-action {
        padding: 12px 16px;
        font-size: 13px;
    }

    /* Notifications on Mobile */
    .notification-item {
        padding: 14px 0;
    }

    .notification-icon {
        width: 36px;
        height: 36px;
    }

    .notification-content h6 {
        font-size: 13px;
    }

    .notification-content p {
        font-size: 12px;
    }

    /* Charts on Mobile */
    .chart-container {
        min-height: 250px;
    }

    .chart-card h5 {
        font-size: 14px;
    }
}

/* Tablet Adjustments */
@media (max-width: 768px) {
    .table-card .card-header h5,
    .chart-card h5,
    .quick-actions-card h5,
    .notifications-card h5 {
        font-size: 15px;
    }

    .custom-table {
        font-size: 13px;
    }

    .custom-table thead th,
    .custom-table tbody td {
        padding: 10px 8px;
    }

    .btn-action {
        padding: 4px 8px;
        font-size: 12px;
    }

    .badge-status {
        font-size: 10px;
        padding: 4px 10px;
    }
}

/* Ensure rows don't break layout */
.row {
    margin-left: 0;
    margin-right: 0;
}

.row > * {
    padding-left: calc(var(--bs-gutter-x, 0.75rem) * 0.5);
    padding-right: calc(var(--bs-gutter-x, 0.75rem) * 0.5);
}
</style>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fees vs Collections
    const feesCtx = document.getElementById('feesPieChart').getContext('2d');
    new Chart(feesCtx, {
        type: 'pie',
        data: {
            labels: ['Collected', 'Outstanding'],
            datasets: [{
                data: [{{ $totalFeesCollected }}, {{ $outstandingBalances }}],
                backgroundColor: ['#79c347', '#ff4748'],
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
                            size: 12,
                            family: 'Poppins'
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
                @foreach($expensesByCategory as $exp)
                    '{{ $exp->category->name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($expensesByCategory as $exp)
                        {{ $exp->total }},
                    @endforeach
                ],
                backgroundColor: ['#36a9e2','#8e68ef','#fabb3d','#ff4748','#79c347','#f2994a'],
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
                            size: 12,
                            family: 'Poppins'
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
                data: [{{ implode(',', $monthlyNet) }}],
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
                            size: 12,
                            family: 'Poppins'
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
                            size: 11,
                            family: 'Poppins'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            family: 'Poppins'
                        }
                    }
                }
            }
        }
    });
</script>
@endsection