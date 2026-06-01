@extends('layouts.app')
@section('main')

<div class="main-wrapper">

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <h4 class="mb-1" id="page-title">Promoting students...</h4>
        <p class="text-muted mb-0" id="page-sub">
            Please wait while the system processes all students. Do not close this tab.
        </p>
    </div>

    {{-- Promotion context --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex align-items-center gap-4 flex-wrap" style="font-size:13px;">
                <span>
                    <span class="text-muted">Type:</span>
                    <strong>{{ $run->type === 'term_promotion' ? 'Term promotion' : 'Year promotion' }}</strong>
                </span>
                <span>
                    <span class="text-muted">From:</span>
                    <strong>{{ $run->fromTerm ? $run->fromTerm->name . ' — ' . $run->fromTerm->year : '—' }}</strong>
                </span>
                <span>
                    <span class="text-muted">To:</span>
                    <strong>{{ $run->toTerm ? $run->toTerm->name . ' — ' . $run->toTerm->year : '—' }}</strong>
                </span>
                <span>
                    <span class="text-muted">Initiated by:</span>
                    <strong>{{ $run->promotedBy ? $run->promotedBy->name : 'System' }}</strong>
                </span>
            </div>
        </div>
    </div>

    {{-- Stage Breadcrumb --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="stage-track">
                <div class="stage stage-done">
                    <div class="stage-circle done"><i class="fa fa-check"></i></div>
                    <div class="stage-label">Confirmed</div>
                </div>
                <div class="stage-line done"></div>
                <div class="stage stage-done">
                    <div class="stage-circle done"><i class="fa fa-check"></i></div>
                    <div class="stage-label">Validated</div>
                </div>
                <div class="stage-line active" id="line-enroll"></div>
                <div class="stage stage-active" id="st-enroll">
                    <div class="stage-circle active" id="circle-enroll">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                    <div class="stage-label">Enrolling</div>
                </div>
                <div class="stage-line" id="line-invoice"></div>
                <div class="stage" id="st-invoice">
                    <div class="stage-circle" id="circle-invoice">
                        <i class="fa fa-file-invoice"></i>
                    </div>
                    <div class="stage-label">Invoicing</div>
                </div>
                <div class="stage-line" id="line-done"></div>
                <div class="stage" id="st-done">
                    <div class="stage-circle" id="circle-done">
                        <i class="fa fa-flag-checkered"></i>
                    </div>
                    <div class="stage-label">Done</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress + Summary --}}
    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-tasks me-2"></i>Progress</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span id="prog-label" class="text-muted">Starting...</span>
                        <span id="prog-pct" style="font-weight:600;color:var(--primary-color);">0%</span>
                    </div>
                    <div class="progress-track mb-3">
                        <div class="progress-fill" id="prog-fill" style="width:0%;"></div>
                    </div>
                    <div style="font-size:13px;color:var(--gray-500);" class="mb-4">
                        <span id="prog-count" style="font-weight:600;color:var(--gray-900);">0</span>
                        of
                        <span id="prog-total" style="font-weight:600;color:var(--gray-900);">—</span>
                        students processed
                    </div>

                    {{-- Per-class batch rows — rendered by JS from poll response --}}
                    <div class="batch-list" id="batch-list">
                        {{-- Initial render from server — real classes, all waiting --}}
                        @foreach($classes as $class)
                            <div class="batch-row batch-wait" id="batch-{{ $class->id }}">
                                <span class="batch-dot dot-wait"></span>
                                <span class="batch-name">
                                    {{ $class->name }}
                                    @if($class->is_final) — Final year @endif
                                </span>
                                <span class="batch-status text-muted">Waiting</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-chart-pie me-2"></i>Summary</h5>
                </div>
                <div class="card-body">
                    <div class="summary-stat">
                        <div class="ss-label">Total to process</div>
                        <div class="ss-val" id="sum-total">—</div>
                    </div>
                    <div class="summary-stat">
                        <div class="ss-label">Processed so far</div>
                        <div class="ss-val text-success" id="sum-processed">0</div>
                    </div>
                    <div class="summary-stat">
                        <div class="ss-label">Enrolled</div>
                        <div class="ss-val" id="sum-enrolled" style="color:var(--primary-color);">0</div>
                    </div>
                    <div class="summary-stat">
                        <div class="ss-label">Skipped (inactive)</div>
                        <div class="ss-val text-muted" id="sum-skipped">0</div>
                    </div>
                    <div class="summary-stat">
                        <div class="ss-label">Errors</div>
                        <div class="ss-val" id="sum-errors">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action footer — hidden until done --}}
    <div id="action-footer" class="d-none">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div id="footer-message" style="font-size:14px;font-weight:500;"></div>
                    <div class="d-flex gap-2" id="footer-buttons"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
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
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

.page-header h4 { font-size: 24px; font-weight: 600; color: var(--gray-900); margin: 0; }
.page-header p  { font-size: 14px; color: var(--gray-500); }

.card         { border: 1px solid var(--gray-200); border-radius: var(--border-radius); box-shadow: 0 1px 3px rgba(0,0,0,.05); }
.card-header  { background: var(--gray-50); border-bottom: 1px solid var(--gray-200); padding: 14px 20px; }
.card-body    { padding: 20px; }
.card-header h5 { font-size: 15px; font-weight: 600; color: var(--gray-900); margin: 0; }

.btn { border-radius: var(--border-radius); padding: 8px 16px; font-size: 14px; font-weight: 500; }
.btn-primary          { background: var(--primary-color); border-color: var(--primary-color); color: white; }
.btn-primary:hover    { background: #2a8cbd; }
.btn-outline-secondary{ color: var(--gray-700); border-color: var(--gray-300); background: white; }
.btn-outline-secondary:hover { background: var(--gray-50); }

.stage-track  { display: flex; align-items: center; padding: 4px 0; }
.stage        { display: flex; flex-direction: column; align-items: center; gap: 6px; flex-shrink: 0; }
.stage-circle {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 500;
    background: var(--gray-100); color: var(--gray-400);
    border: 2px solid var(--gray-200); transition: all .3s;
}
.stage-circle.done   { background: #e8f5e0; color: var(--success-dark); border-color: var(--success-color); }
.stage-circle.active { background: #eff6ff; color: var(--primary-color); border-color: var(--primary-color); }
.stage-circle.error  { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.stage-label  { font-size: 11px; font-weight: 500; color: var(--gray-500); white-space: nowrap; }
.stage-done   .stage-label { color: var(--success-dark); }
.stage-active .stage-label { color: var(--primary-color); }

.stage-line { flex: 1; height: 2px; background: var(--gray-200); margin: 0 4px; margin-top: -18px; transition: background .3s; }
.stage-line.done   { background: var(--success-color); }
.stage-line.active { background: linear-gradient(to right, var(--success-color), var(--primary-color)); }
.stage-line.error  { background: var(--danger-color); }

.progress-track { height: 10px; background: var(--gray-100); border-radius: 5px; overflow: hidden; border: 1px solid var(--gray-200); }
.progress-fill  { height: 100%; border-radius: 5px; background: var(--primary-color); transition: width .4s ease, background .3s; }
.progress-fill.done  { background: var(--success-color); }
.progress-fill.error { background: var(--danger-color); }

.batch-list { display: flex; flex-direction: column; gap: 6px; }
.batch-row  { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: var(--border-radius); font-size: 13px; border: 1px solid var(--gray-200); transition: background .2s; }
.batch-row.batch-done   { background: #f0fdf4; border-color: #bbf7d0; }
.batch-row.batch-active { background: #eff6ff; border-color: #bfdbfe; }
.batch-row.batch-wait   { background: var(--gray-50); }
.batch-row.batch-error  { background: #fff1f2; border-color: #fecdd3; }
.batch-name   { font-weight: 500; color: var(--gray-900); flex: 1; }
.batch-status { font-size: 12px; }
.batch-dot    { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.dot-done   { background: var(--success-color); }
.dot-active { background: var(--primary-color); animation: pulse 1s ease-in-out infinite; }
.dot-wait   { background: var(--gray-300); }
.dot-error  { background: var(--danger-color); }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

.summary-stat { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
.summary-stat:last-child { border-bottom: none; }
.ss-label { color: var(--gray-500); }
.ss-val   { font-weight: 600; font-size: 16px; color: var(--gray-900); }

@media (max-width: 768px) {
    .stage-label  { font-size: 10px; }
    .stage-circle { width: 28px; height: 28px; font-size: 12px; }
}
</style>

<script>
// Poll endpoint URL — passed from blade
const pollUrl  = '{{ route("promotion.poll", $run->id) }}';
const enrollmentUrl = '{{ route("enrollment.index") }}';
const invoicesUrl   = '{{ route("invoices.index") }}';

let pollInterval = null;
let completed    = false;

// Start polling immediately on page load
document.addEventListener('DOMContentLoaded', function () {
    poll(); // first call immediately
    pollInterval = setInterval(poll, 2000); // then every 2 seconds
});

function poll() {
    fetch(pollUrl, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        updateProgress(data);

        if (data.state === 'done' || data.state === 'error' || data.state === 'failed') {
            clearInterval(pollInterval);
            completed = true;
            showFooter(data);
        }
    })
    .catch(err => {
        console.error('Poll error:', err);
        // Do not stop polling on network blip — try again next interval
    });
}

function updateProgress(data) {
    const fill  = document.getElementById('prog-fill');
    const pct   = document.getElementById('prog-pct');
    const label = document.getElementById('prog-label');

    // Progress bar
    fill.style.width = data.pct + '%';
    fill.className   = 'progress-fill' + (data.state === 'done' ? ' done' : data.state === 'error' ? ' error' : '');
    pct.textContent  = data.pct + '%';
    pct.style.color  = data.state === 'done' ? 'var(--success-dark)' : data.state === 'error' ? '#991b1b' : 'var(--primary-color)';

    // Count labels
    document.getElementById('prog-count').textContent  = data.total_processed;
    document.getElementById('prog-total').textContent  = data.total_expected;
    document.getElementById('sum-total').textContent    = data.total_expected;
    document.getElementById('sum-processed').textContent = data.total_processed;
    document.getElementById('sum-enrolled').textContent  = data.total_processed - data.errors;
    document.getElementById('sum-skipped').textContent   = data.skipped;

    const errEl = document.getElementById('sum-errors');
    errEl.textContent = data.errors;
    errEl.className   = data.errors > 0 ? 'ss-val text-danger' : 'ss-val';

    // Progress label text
    if (data.state === 'running') {
        const active = data.batches.find(b => b.state === 'active');
        label.textContent = active ? 'Processing ' + active.class_name + '...' : 'Processing...';
    } else if (data.state === 'done') {
        label.textContent = 'All classes processed.';
        document.getElementById('page-title').textContent = 'Promotion complete';
        document.getElementById('page-sub').textContent   = 'All ' + data.total_processed + ' students successfully enrolled and invoiced.';
    } else if (data.state === 'error') {
        label.textContent = 'Completed — ' + data.errors + ' error' + (data.errors > 1 ? 's' : '') + ' found.';
        document.getElementById('page-title').textContent = 'Promotion completed with errors';
        document.getElementById('page-sub').textContent   = data.errors + ' student' + (data.errors > 1 ? 's' : '') + ' could not be processed. Review from the enrollment screen.';
    } else if (data.state === 'failed') {
        label.textContent = data.message || 'Promotion failed.';
        document.getElementById('page-title').textContent = 'Promotion failed';
        document.getElementById('page-sub').textContent   = data.message || 'The promotion job could not complete. Please try again or contact support.';
    } else if (data.state === 'pending') {
        label.textContent = data.message || 'Queued — waiting to start...';
    }

    // Stage track
    updateStages(data.state);

    // Batch rows
    updateBatches(data.batches);
}

function updateStages(state) {
    const stEnroll     = document.getElementById('st-enroll');
    const circleEnroll = document.getElementById('circle-enroll');
    const stInvoice    = document.getElementById('st-invoice');
    const circleInvoice= document.getElementById('circle-invoice');
    const stDone       = document.getElementById('st-done');
    const circleDone   = document.getElementById('circle-done');
    const lineInvoice  = document.getElementById('line-invoice');
    const lineDone     = document.getElementById('line-done');

    if (state === 'running') {
        // Enrolling active
        stEnroll.className      = 'stage stage-active';
        circleEnroll.className  = 'stage-circle active';
        circleEnroll.innerHTML  = '<i class="fa fa-spinner fa-spin"></i>';
        stInvoice.className     = 'stage';
        circleInvoice.className = 'stage-circle';
        stDone.className        = 'stage';
        circleDone.className    = 'stage-circle';
        lineInvoice.className   = 'stage-line';
        lineDone.className      = 'stage-line';
    } else if (state === 'done') {
        stEnroll.className      = 'stage stage-done';
        circleEnroll.className  = 'stage-circle done';
        circleEnroll.innerHTML  = '<i class="fa fa-check"></i>';
        stInvoice.className     = 'stage stage-done';
        circleInvoice.className = 'stage-circle done';
        circleInvoice.innerHTML = '<i class="fa fa-check"></i>';
        stDone.className        = 'stage stage-done';
        circleDone.className    = 'stage-circle done';
        circleDone.innerHTML    = '<i class="fa fa-check"></i>';
        lineInvoice.className   = 'stage-line done';
        lineDone.className      = 'stage-line done';
    } else if (state === 'error') {
        stEnroll.className      = 'stage stage-done';
        circleEnroll.className  = 'stage-circle done';
        circleEnroll.innerHTML  = '<i class="fa fa-check"></i>';
        stInvoice.className     = 'stage stage-done';
        circleInvoice.className = 'stage-circle done';
        circleInvoice.innerHTML = '<i class="fa fa-check"></i>';
        stDone.className        = 'stage';
        circleDone.className    = 'stage-circle error';
        circleDone.innerHTML    = '<i class="fa fa-exclamation-triangle"></i>';
        lineInvoice.className   = 'stage-line done';
        lineDone.className      = 'stage-line error';
    } else if (state === 'failed') {
        stEnroll.className      = 'stage';
        circleEnroll.className  = 'stage-circle error';
        circleEnroll.innerHTML  = '<i class="fa fa-exclamation-triangle"></i>';
        stInvoice.className     = 'stage';
        circleInvoice.className = 'stage-circle';
        stDone.className        = 'stage';
        circleDone.className    = 'stage-circle';
        lineInvoice.className   = 'stage-line error';
        lineDone.className      = 'stage-line';
    }
}

function updateBatches(batches) {
    batches.forEach(function(b) {
        const row = document.getElementById('batch-' + b.class_id);
        if (!row) return;

        // Reset classes
        row.className = 'batch-row';
        const dot    = row.querySelector('.batch-dot');
        const status = row.querySelector('.batch-status');

        if (b.state === 'done') {
            row.classList.add('batch-done');
            dot.className   = 'batch-dot dot-done';
            status.className= 'batch-status text-success';
            status.textContent = b.done + ' enrolled \u00b7 ' + b.done + ' invoiced';
        } else if (b.state === 'active') {
            row.classList.add('batch-active');
            dot.className   = 'batch-dot dot-active';
            status.className= 'batch-status';
            status.style.color = 'var(--primary-color)';
            status.textContent = b.done + ' of ' + b.expected + ' processing...';
        } else if (b.state === 'error') {
            row.classList.add('batch-error');
            dot.className   = 'batch-dot dot-error';
            status.className= 'batch-status text-danger';
            status.innerHTML = (b.done - b.failed) + ' done &middot; <strong>' + b.failed + ' failed</strong>';
        } else {
            row.classList.add('batch-wait');
            dot.className   = 'batch-dot dot-wait';
            status.className= 'batch-status text-muted';
            status.style.color = '';
            status.textContent = 'Waiting';
        }
    });
}

function showFooter(data) {
    const footer  = document.getElementById('action-footer');
    const msg     = document.getElementById('footer-message');
    const buttons = document.getElementById('footer-buttons');

    footer.classList.remove('d-none');

    if (data.state === 'done') {
        msg.className = 'text-success';
        msg.innerHTML = '<i class="fa fa-check-circle me-2"></i>Promotion completed successfully.';
        buttons.innerHTML =
            '<a href="' + enrollmentUrl + '" class="btn btn-outline-secondary"><i class="fa fa-users me-1"></i>View enrollment list</a>' +
            '<a href="' + invoicesUrl   + '" class="btn btn-primary"><i class="fa fa-file-invoice-dollar me-1"></i>View invoices generated</a>';
    } else if (data.state === 'failed') {
        msg.className = 'text-danger';
        msg.innerHTML = '<i class="fa fa-exclamation-circle me-2"></i>' + (data.message || 'Promotion failed.');
        buttons.innerHTML =
            '<button type="button" class="btn btn-outline-secondary" onclick="history.back()"><i class="fa fa-arrow-left me-1"></i>Go back</button>';
    } else {
        msg.className = 'text-danger';
        msg.innerHTML = '<i class="fa fa-exclamation-circle me-2"></i>' + data.errors + ' student' + (data.errors > 1 ? 's' : '') + ' need correction.';
        buttons.innerHTML =
            '<a href="' + enrollmentUrl + '?status=wrongly_promoted" class="btn btn-outline-secondary" style="color:#991b1b;border-color:#fca5a5;"><i class="fa fa-wrench me-1"></i>Review ' + data.errors + ' error' + (data.errors > 1 ? 's' : '') + '</a>' +
            '<a href="' + enrollmentUrl + '" class="btn btn-primary"><i class="fa fa-users me-1"></i>View enrollment list</a>';
    }
}
</script>

@endsection