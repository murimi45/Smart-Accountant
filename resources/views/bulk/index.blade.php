@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <h4 class="mb-1">Bulk Import / Export</h4>
        <p class="text-muted mb-0">Download CSV templates, export data, or import students, class fees, and payments</p>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('import_errors'))
        <div class="alert alert-danger mb-4">
            <i class="fa fa-exclamation-triangle me-2"></i>
            <strong>Import issues:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Students / Fees / Payments --}}
    <div class="row g-4">
        @if($isAdmin)
        <div class="col-lg-4">
            <div class="card form-card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-users me-2"></i>Students</h5></div>
                <div class="card-body">
                    <p class="text-muted small">Import new students with class & term enrollment, or export all student records.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('bulk.template', 'students') }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-download me-1"></i>Download template</a>
                        <a href="{{ route('bulk.export', 'students') }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-file-export me-1"></i>Export CSV</a>
                    </div>
                    <hr>
                    <form action="{{ route('bulk.import', 'students') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="form-label small">Import CSV</label>
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-success btn-sm w-100"><i class="fa fa-upload me-1"></i>Import students</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="col-lg-4">
            <div class="card form-card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-file-invoice-dollar me-2"></i>Fees</h5></div>
                <div class="card-body">
                    <p class="text-muted small">
                        <strong>Export</strong> student invoices (balances). <strong>Import</strong> class fee amounts per class/term (updates invoices automatically).
                    </p>
                    <form method="GET" action="{{ route('bulk.export', 'fees') }}" class="mb-2">
                        <label class="form-label small">Term filter (export)</label>
                        <select name="term_id" class="form-select form-select-sm mb-2">
                            <option value="">All terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa fa-file-export me-1"></i>Export fee invoices</button>
                    </form>
                    <a href="{{ route('bulk.template', 'fees') }}" class="btn btn-outline-secondary btn-sm w-100 mb-2"><i class="fa fa-download me-1"></i>Download class-fee template</a>
                    <form action="{{ route('bulk.import', 'fees') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-success btn-sm w-100"><i class="fa fa-upload me-1"></i>Import class fees</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card form-card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-money-bill-wave me-2"></i>Payments</h5></div>
                <div class="card-body">
                    <p class="text-muted small">Bulk record fee payments by admission number, or export payment history.</p>
                    <form method="GET" action="{{ route('bulk.export', 'payments') }}" class="mb-2">
                        <label class="form-label small">Term filter (export)</label>
                        <select name="term_id" class="form-select form-select-sm mb-2">
                            <option value="">All terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa fa-file-export me-1"></i>Export payments</button>
                    </form>
                    <a href="{{ route('bulk.template', 'payments') }}" class="btn btn-outline-secondary btn-sm w-100 mb-2"><i class="fa fa-download me-1"></i>Download template</a>
                    <form action="{{ route('bulk.import', 'payments') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-success btn-sm w-100"><i class="fa fa-upload me-1"></i>Import payments</button>
                    </form>
                    <p class="text-muted small mt-2 mb-0">Payments apply to the current term unless you include a <code>term</code> column (e.g. <code>Term 1 - 2026</code>).</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Opening Balances --}}
    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="card form-card form-card-warning h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-history me-2"></i>Opening balances (prior arrears)</h5></div>
                <div class="card-body">
                    <p class="text-muted small">
                        Load what students owed <strong>before</strong> this system (or before the current year). Adds an
                        <em>Opening balance (prior arrears)</em> line to their term invoice on top of current class fees.
                    </p>
                    <form method="GET" action="{{ route('bulk.export', 'opening_balances') }}" class="mb-2">
                        <label class="form-label small">Term filter (export)</label>
                        <select name="term_id" class="form-select form-select-sm mb-2">
                            <option value="">All terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa fa-file-export me-1"></i>Export opening balances</button>
                    </form>
                    <a href="{{ route('bulk.template', 'opening_balances') }}" class="btn btn-outline-secondary btn-sm w-100 mb-2"><i class="fa fa-download me-1"></i>Download template</a>
                    <form action="{{ route('bulk.import', 'opening_balances') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-warning btn-sm w-100"><i class="fa fa-upload me-1"></i>Import opening balances</button>
                    </form>
                    <p class="text-muted small mt-2 mb-0">Student must already exist and be enrolled for the term. Re-importing updates the amount.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CSV Tips --}}
    <div class="card form-card mt-4">
        <div class="card-header"><h5 class="mb-0"><i class="fa fa-circle-info me-2"></i>CSV Tips</h5></div>
        <div class="card-body">
            <ul class="info-list small mb-0">
                <li>Use the template headers exactly — open in Excel or Google Sheets, save as CSV UTF-8.</li>
                <li><strong>Class</strong> and <strong>term</strong> must match names in the system (e.g. <code>Grade 1</code>, <code>Term 1 - 2026</code>).</li>
                <li>Duplicate student admissions update the existing record and add enrollment for a new term if needed.</li>
                <li>Payment import uses the same rules as manual payment entry (current term invoice, not voided).</li>
                <li><strong>Opening balances</strong> are for old debt only — use the current term column (e.g. first term you are billing in the system).</li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Base Variables — matched to Admin Management page */
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
    --gray-400: #9ca3af;
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

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 12px 16px;
}

.alert-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Cards */
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
    margin: 0;
}

.form-card .card-body {
    padding: 20px;
}

/* Warning Variant (e.g. opening balances) */
.form-card-warning {
    border-color: #fcd34d;
}

.form-card-warning .card-header {
    background: #fef3c7;
    border-bottom-color: #fcd34d;
}

.form-card-warning .card-header h5 {
    color: #92400e;
}

/* Form Elements */
.form-card .form-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-card .form-control,
.form-card .form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    font-size: 13px;
}

.form-card .form-control:focus,
.form-card .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Buttons */
.btn {
    border-radius: var(--border-radius);
    font-weight: 500;
    transition: all 0.2s;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
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
    color: #fff;
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
    color: #fff;
}

.btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #fff;
}

.btn-warning:hover {
    background-color: #d97706;
    border-color: #d97706;
    color: #fff;
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
    background: white;
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
}

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-700);
}

/* Info List (CSV Tips) */
.info-list {
    color: var(--gray-600);
    padding-left: 18px;
    line-height: 1.7;
}

.info-list code {
    background: var(--gray-100);
    color: var(--gray-700);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header h4 {
        font-size: 20px;
    }

    .form-card .card-body {
        padding: 16px;
    }
}
</style>
@endsection