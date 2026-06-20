@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Automated fee reminders</h4>
        <p class="text-muted mb-0">Schedule SMS to parents when balances are overdue</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-bell me-2"></i>Reminder settings</h5>
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
                            <label class="form-label">Days before first reminder</label>
                            <input type="number" name="min_days_outstanding" class="form-control"
                                   min="0" max="365" required
                                   value="{{ old('min_days_outstanding', $setting->min_days_outstanding) }}">
                            <div class="form-text">Invoice must be at least this many days old (from invoice date).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum days between reminders (same invoice)</label>
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>Save settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-info">
                <div class="card-header bg-info-subtle">
                    <h5 class="mb-0"><i class="fa fa-play me-2"></i>Run now</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Queue overdue SMS immediately using the saved rules above. Useful after enabling for the first time.
                    </p>
                    <form action="{{ route('reminders.run') }}" method="POST"
                          onsubmit="return confirm('Queue overdue SMS for all eligible parents now?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-info w-100" {{ $setting->enabled ? '' : 'disabled' }}>
                            <i class="fa fa-paper-plane me-1"></i>Send overdue reminders now
                        </button>
                    </form>
                    @unless($setting->enabled)
                        <p class="text-muted small mt-2 mb-0">Enable reminders first.</p>
                    @endunless
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="text-muted">How it works</h6>
                    <ul class="small text-muted mb-0">
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
@endsection
