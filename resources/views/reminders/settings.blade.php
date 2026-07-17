@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">Automated Fee Reminders</h4>
                <p class="text-muted mb-0">Schedule SMS to parents when balances are overdue</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('sms.logs') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-list me-2"></i>View SMS Logs
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Reminder Settings Card --}}
        <div class="col-lg-7">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-bell me-2"></i>Reminder Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reminders.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled"
                                   {{ old('enabled', $setting->enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enabled">
                                <strong>Enable automated overdue SMS</strong>
                            </label>
                            <div class="form-text">Runs daily via the system scheduler (default 9:00 AM).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-clock me-1"></i>Days before first reminder</label>
                            <input type="number" name="min_days_outstanding" class="form-control"
                                   min="0" max="365" required
                                   value="{{ old('min_days_outstanding', $setting->min_days_outstanding) }}">
                            <div class="form-text">Invoice must be at least this many days old (from invoice date).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-redo me-1"></i>Minimum days between reminders (same invoice)</label>
                            <input type="number" name="reminder_interval_days" class="form-control"
                                   min="1" max="90" required
                                   value="{{ old('reminder_interval_days', $setting->reminder_interval_days) }}">
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="current_term_only" value="1" id="current_term_only"
                                   {{ old('current_term_only', $setting->current_term_only) ? 'checked' : '' }}>
                            <label class="form-check-label" for="current_term_only">
                                Current term invoices only
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save me-2"></i>Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-5">
            {{-- Run Now Card --}}
            <div class="card table-card info-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-play me-2"></i>Run Now</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Queue overdue SMS immediately using the saved rules above. Useful after enabling for the first time.
                    </p>
                    <form action="{{ route('reminders.run') }}" method="POST"
                          onsubmit="return confirm('Queue overdue SMS for all eligible parents now?');">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" {{ $setting->enabled ? '' : 'disabled' }}>
                            <i class="fa fa-paper-plane me-2"></i>Send Overdue Reminders Now
                        </button>
                    </form>
                    @unless($setting->enabled)
                        <p class="text-muted small mt-2 mb-0">Enable reminders first.</p>
                    @endunless
                </div>
            </div>

            {{-- How It Works Card --}}
            <div class="card table-card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-circle-info me-2"></i>How It Works</h5>
                </div>
                <div class="card-body">
                    <ul class="info-list small mb-0">
                        <li>Only students with a phone number on file are contacted.</li>
                        <li>Messages are logged under <a href="{{ route('sms.logs') }}">SMS Logs</a>.</li>
                        <li>Manual bulk SMS from Fee Summary still works independently.</li>
                        <li>Ensure <code>php artisan schedule:run</code> runs every minute on your server.</li>
                    </ul>
                </div>
            </div>
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
    --info-color: #36a9e2;
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
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.table-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.table-card .card-body {
    padding: 20px;
}

.info-card .card-header {
    background: rgba(54, 169, 226, 0.08);
    border-bottom: 1px solid rgba(54, 169, 226, 0.2);
}

.info-card .card-header h5 {
    color: #2a8cbd;
}

/* Form Elements */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.form-text {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
}

.form-check-input:checked {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.form-check-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.form-check-label {
    font-size: 14px;
    color: var(--gray-700);
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

.btn-primary:disabled {
    background-color: var(--gray-300);
    border-color: var(--gray-300);
    color: var(--gray-500);
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

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
    padding: 8px 16px;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-700);
}

/* Info List */
.info-list {
    color: var(--gray-600);
    padding-left: 18px;
    line-height: 1.7;
}

.info-list a {
    color: var(--primary-color);
    text-decoration: none;
}

.info-list a:hover {
    text-decoration: underline;
}

.info-list code {
    background: var(--gray-100);
    color: var(--gray-700);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
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

/* Responsive Design */
@media (max-width: 768px) {
    .table-card .card-body {
        padding: 16px;
    }
}
</style>
@endsection