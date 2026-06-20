@extends('layouts.app')

@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header">
        <h4>Welcome back!</h4>
        <p>Here's your School Accounts overview</p>
    </div>

    {{-- Filter Card --}}
    <div class="card filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-eye me-1"></i>View Type</label>
                        <select name="view" class="form-select" onchange="this.form.submit()">
                            <option value="term" {{ $viewType === 'term' ? 'selected' : '' }}>Term View</option>
                            <option value="annual" {{ $viewType === 'annual' ? 'selected' : '' }}>Annual View</option>
                        </select>
                    </div>

                    @if($viewType === 'term')
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Select Term</label>
                        <select name="term_id" class="form-select" onchange="this.form.submit()">
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}"
                                    {{ isset($selectedTerm) && $selectedTerm->id == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} ({{ $term->year }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar-alt me-1"></i>Select Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}"
                                    {{ isset($selectedYear) && $selectedYear->id == $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Primary KPI Cards --}}
    <div class="section-block">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Total Fees Billed</span>
                        <span class="kpi-icon kpi-icon-neutral"><i class="fa fa-credit-card"></i></span>
                    </div>
                    <div class="kpi-value">KSh {{ number_format($totalFeesBilled, 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Fees Collected</span>
                        <span class="kpi-icon kpi-icon-success"><i class="fa fa-money-bill-wave"></i></span>
                    </div>
                    <div class="kpi-value">KSh {{ number_format($totalFeesCollected, 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Outstanding Balance</span>
                        <span class="kpi-icon kpi-icon-danger"><i class="fa fa-balance-scale"></i></span>
                    </div>
                    <div class="kpi-value">KSh {{ number_format($outstandingBalances, 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card kpi-card-accent">
                    <div class="kpi-top">
                        <span class="kpi-label">Net Position</span>
                        <span class="kpi-icon kpi-icon-warning"><i class="fa fa-chart-line"></i></span>
                    </div>
                    <div class="kpi-value">KSh {{ number_format($netPosition, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="section-block">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-row-card">
                    <span class="stat-row-icon stat-row-icon-success"><i class="fa fa-plus-circle"></i></span>
                    <div class="stat-row-content">
                        <div class="stat-row-label">Other Income</div>
                        <div class="stat-row-value">KSh {{ number_format($otherIncome, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-row-card">
                    <span class="stat-row-icon stat-row-icon-danger"><i class="fa fa-minus-circle"></i></span>
                    <div class="stat-row-content">
                        <div class="stat-row-label">Total Expenses</div>
                        <div class="stat-row-value">KSh {{ number_format($totalExpenses, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Row --}}
    <div class="section-block">
        <div class="row g-3">
            {{-- Recent Payments Table --}}
            <div class="col-xl-8">
                <div class="card table-card h-100">
                    <div class="card-header">
                        <h5><i class="fa fa-receipt me-2"></i>Recent Payments</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table dashboard-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th class="d-none d-lg-table-cell">Class</th>
                                        <th class="text-end">Amount</th>
                                        <th class="d-none d-md-table-cell">Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <span class="student-id">{{ $payment->student?->student_id ?? 'N/A' }}</span>
                                        </td>
                                        <td class="student-name">
                                            {{ $payment->student?->name ?? 'Unknown Student' }}
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            <span class="badge-class">{{ $payment->student?->class_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="amount-text">KSh {{ number_format($payment->amount) }}</span>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <span class="date-text">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge
                                                {{ $payment->status === 'Paid' ? 'badge-success' : ($payment->status === 'Pending' ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $payment->status ?? 'Unknown' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No recent payments found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column gap-3 h-100">
                    {{-- Quick Actions --}}
                    <div class="card quick-actions-card">
                        <div class="card-header">
                            <h5><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body p-2">
                            <button class="action-row">
                                <span class="action-row-icon action-row-icon-primary"><i class="fa fa-plus-circle"></i></span>
                                <span class="action-row-label">Record New Payment</span>
                                <i class="fa fa-chevron-right action-row-chevron"></i>
                            </button>
                            <button class="action-row">
                                <span class="action-row-icon action-row-icon-success"><i class="fa fa-file-invoice"></i></span>
                                <span class="action-row-label">Generate Invoice</span>
                                <i class="fa fa-chevron-right action-row-chevron"></i>
                            </button>
                            <button class="action-row">
                                <span class="action-row-icon action-row-icon-warning"><i class="fa fa-user-plus"></i></span>
                                <span class="action-row-label">Add Student</span>
                                <i class="fa fa-chevron-right action-row-chevron"></i>
                            </button>
                            <button class="action-row">
                                <span class="action-row-icon action-row-icon-neutral"><i class="fa fa-download"></i></span>
                                <span class="action-row-label">Export Report</span>
                                <i class="fa fa-chevron-right action-row-chevron"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Notifications --}}
                    <livewire:dashboard-notifications />
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="section-block">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5><i class="fa fa-pie-chart me-2"></i>Fees vs Collections</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="feesPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5><i class="fa fa-shopping-cart me-2"></i>Expenses Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="expensesPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-block">
        <div class="row g-3">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5><i class="fa fa-chart-line me-2"></i>Net Position Over Time</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="netLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   DESIGN TOKENS
   ============================================ */
:root {
    --primary: #36a9e2;
    --primary-dark: #2a8cbd;
    --success: #79c347;
    --success-dark: #5fa732;
    --danger: #ef4444;
    --warning: #f59e0b;

    --ink-900: #0f172a;
    --ink-700: #334155;
    --ink-600: #475569;
    --ink-500: #64748b;
    --ink-400: #94a3b8;

    --surface: #ffffff;
    --surface-muted: #f8fafc;
    --border: #e2e8f0;
    --border-soft: #eef1f5;

    --radius: 10px;
    --radius-sm: 7px;

    --shadow-card: 0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.04);
    --shadow-card-hover: 0 4px 10px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.05);

    --space-1: 4px;
    --space-2: 8px;
    --space-3: 12px;
    --space-4: 16px;
    --space-5: 20px;
    --space-6: 24px;
    --space-8: 32px;
}

.main-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
}

.section-block {
    margin-bottom: var(--space-6);
}

/* ============================================
   PAGE HEADER
   ============================================ */
.page-header {
    margin-bottom: var(--space-6);
}

.page-header h4 {
    font-size: 22px;
    font-weight: 650;
    color: var(--ink-900);
    margin: 0 0 var(--space-1) 0;
    letter-spacing: -0.01em;
}

.page-header p {
    font-size: 14px;
    color: var(--ink-500);
    margin: 0;
}

/* ============================================
   UNIFIED CARD SYSTEM
   ============================================ */
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
}

.filter-card {
    margin-bottom: var(--space-6);
}

.filter-card .card-body {
    padding: var(--space-5);
}

.card-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border-soft);
    padding: var(--space-4) var(--space-5);
}

.card-header h5 {
    font-size: 14.5px;
    font-weight: 600;
    color: var(--ink-900);
    margin: 0;
    letter-spacing: -0.005em;
}

.card-header h5 i {
    color: var(--ink-400);
    font-size: 13px;
}

/* ============================================
   FORM ELEMENTS
   ============================================ */
.form-label {
    font-size: 12.5px;
    font-weight: 550;
    color: var(--ink-600);
    margin-bottom: var(--space-2);
}

.form-control,
.form-select {
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 8px 12px;
    font-size: 13.5px;
    color: var(--ink-900);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.12);
}

/* ============================================
   PRIMARY KPI CARDS
   ============================================ */
.kpi-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    padding: var(--space-5);
    height: 100%;
    transition: box-shadow 0.18s ease, transform 0.18s ease;
}

.kpi-card:hover {
    box-shadow: var(--shadow-card-hover);
    transform: translateY(-1px);
}

.kpi-card-accent {
    border-color: rgba(54, 169, 226, 0.25);
}

.kpi-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-4);
}

.kpi-label {
    font-size: 12.5px;
    font-weight: 550;
    color: var(--ink-500);
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.kpi-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.kpi-icon i {
    font-size: 13px;
}

.kpi-icon-neutral { background: var(--surface-muted); color: var(--ink-500); }
.kpi-icon-success { background: rgba(121, 195, 71, 0.12); color: var(--success-dark); }
.kpi-icon-danger  { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
.kpi-icon-warning { background: rgba(245, 158, 11, 0.12); color: var(--warning); }

.kpi-value {
    font-size: 21px;
    font-weight: 650;
    color: var(--ink-900);
    letter-spacing: -0.015em;
    font-variant-numeric: tabular-nums;
}

/* ============================================
   SECONDARY STAT ROWS
   ============================================ */
.stat-row-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    padding: var(--space-5);
    display: flex;
    align-items: center;
    gap: var(--space-4);
    transition: box-shadow 0.18s ease, transform 0.18s ease;
}

.stat-row-card:hover {
    box-shadow: var(--shadow-card-hover);
    transform: translateY(-1px);
}

.stat-row-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-row-icon i { font-size: 16px; }

.stat-row-icon-success { background: rgba(121, 195, 71, 0.12); color: var(--success-dark); }
.stat-row-icon-danger  { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

.stat-row-label {
    font-size: 12.5px;
    font-weight: 550;
    color: var(--ink-500);
    margin-bottom: 2px;
}

.stat-row-value {
    font-size: 18px;
    font-weight: 650;
    color: var(--ink-900);
    font-variant-numeric: tabular-nums;
}

/* ============================================
   TABLE — FINTECH-GRADE
   ============================================ */
.table-card .card-body {
    padding: 0;
}

.dashboard-table {
    font-size: 13.5px;
    margin: 0;
}

.dashboard-table thead th {
    background: var(--surface-muted);
    font-weight: 600;
    color: var(--ink-500);
    padding: 11px var(--space-5);
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

.dashboard-table tbody td {
    padding: 13px var(--space-5);
    vertical-align: middle;
    border-bottom: 1px solid var(--border-soft);
    color: var(--ink-700);
}

.dashboard-table tbody tr:last-child td {
    border-bottom: none;
}

.dashboard-table tbody tr {
    transition: background-color 0.12s ease;
}

.dashboard-table tbody tr:hover {
    background-color: var(--surface-muted);
}

.student-id {
    color: var(--ink-500);
    font-weight: 500;
    font-size: 12.5px;
    font-variant-numeric: tabular-nums;
}

.student-name {
    color: var(--ink-900);
    font-weight: 550;
}

.badge-class {
    background-color: var(--surface-muted);
    color: var(--ink-600);
    padding: 3px 9px;
    border-radius: 6px;
    font-weight: 550;
    font-size: 11.5px;
    display: inline-block;
}

.amount-text {
    font-weight: 600;
    color: var(--ink-900);
    font-size: 13.5px;
    font-variant-numeric: tabular-nums;
}

.date-text {
    color: var(--ink-500);
    font-size: 13px;
}

.badge {
    padding: 3px 9px;
    border-radius: 6px;
    font-weight: 550;
    font-size: 11.5px;
}

.badge-success { background-color: rgba(121, 195, 71, 0.12); color: var(--success-dark); }
.badge-warning { background-color: rgba(245, 158, 11, 0.12); color: #92400e; }
.badge-danger  { background-color: rgba(239, 68, 68, 0.1); color: #b91c1c; }

/* ============================================
   QUICK ACTIONS — MODERN ACTION ROWS
   ============================================ */
.action-row {
    width: 100%;
    display: flex;
    align-items: center;
    gap: var(--space-3);
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    padding: 10px var(--space-3);
    text-align: left;
    cursor: pointer;
    transition: background-color 0.14s ease;
}

.action-row:hover {
    background-color: var(--surface-muted);
}

.action-row + .action-row {
    margin-top: 2px;
}

.action-row-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 13px;
}

.action-row-icon-primary { background: rgba(54, 169, 226, 0.12); color: var(--primary-dark); }
.action-row-icon-success { background: rgba(121, 195, 71, 0.12); color: var(--success-dark); }
.action-row-icon-warning { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
.action-row-icon-neutral { background: var(--surface-muted); color: var(--ink-500); }

.action-row-label {
    flex: 1;
    font-size: 13.5px;
    font-weight: 550;
    color: var(--ink-700);
}

.action-row-chevron {
    font-size: 11px;
    color: var(--ink-400);
}

/* ============================================
   CHARTS
   ============================================ */
.chart-card .card-body {
    padding: var(--space-5);
}

.chart-container {
    position: relative;
    width: 100%;
    min-height: 280px;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 992px) {
    .kpi-value { font-size: 19px; }
    .stat-row-value { font-size: 17px; }
}

@media (max-width: 768px) {
    .kpi-card { padding: var(--space-4); }
    .kpi-value { font-size: 17px; }
    .stat-row-card { padding: var(--space-4); }
    .stat-row-value { font-size: 16px; }
    .dashboard-table { font-size: 13px; }
    .dashboard-table thead th,
    .dashboard-table tbody td { padding: 10px 12px; }
    .chart-container { min-height: 240px; }
}

@media (max-width: 576px) {
    .kpi-card { padding: var(--space-3); }
    .kpi-value { font-size: 16px; }
}
</style>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartFont = { family: "-apple-system, 'Inter', 'Segoe UI', Roboto, sans-serif", size: 12 };

    // Fees vs Collections
    const feesCtx = document.getElementById('feesPieChart').getContext('2d');
    new Chart(feesCtx, {
        type: 'pie',
        data: {
            labels: ['Collected', 'Outstanding'],
            datasets: [{
                data: [{{ $totalFeesCollected }}, {{ $outstandingBalances }}],
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
                    labels: { padding: 16, font: chartFont, color: '#475569' }
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
                    labels: { padding: 16, font: chartFont, color: '#475569' }
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
                backgroundColor: 'rgba(54, 169, 226, 0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 5,
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
                    align: 'end',
                    labels: { font: chartFont, color: '#475569', boxWidth: 14 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(15, 23, 42, 0.04)' },
                    ticks: { font: chartFont, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: chartFont, color: '#94a3b8' }
                }
            }
        }
    });
</script>
@endsection