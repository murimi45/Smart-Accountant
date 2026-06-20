<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Automated fee reminders</h4>
        <p class="text-muted mb-0">Schedule SMS to parents when balances are overdue</p>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-bell me-2"></i>Reminder settings</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('reminders.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled"
                                   <?php echo e(old('enabled', $setting->enabled) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="enabled">
                                <strong>Enable automated overdue SMS</strong>
                            </label>
                            <div class="form-text">Runs daily via the system scheduler (default 9:00 AM).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Days before first reminder</label>
                            <input type="number" name="min_days_outstanding" class="form-control"
                                   min="0" max="365" required
                                   value="<?php echo e(old('min_days_outstanding', $setting->min_days_outstanding)); ?>">
                            <div class="form-text">Invoice must be at least this many days old (from invoice date).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum days between reminders (same invoice)</label>
                            <input type="number" name="reminder_interval_days" class="form-control"
                                   min="1" max="90" required
                                   value="<?php echo e(old('reminder_interval_days', $setting->reminder_interval_days)); ?>">
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="current_term_only" value="1" id="current_term_only"
                                   <?php echo e(old('current_term_only', $setting->current_term_only) ? 'checked' : ''); ?>>
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
                    <form action="<?php echo e(route('reminders.run')); ?>" method="POST"
                          onsubmit="return confirm('Queue overdue SMS for all eligible parents now?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-info w-100" <?php echo e($setting->enabled ? '' : 'disabled'); ?>>
                            <i class="fa fa-paper-plane me-1"></i>Send overdue reminders now
                        </button>
                    </form>
                    <?php if (! ($setting->enabled)): ?>
                        <p class="text-muted small mt-2 mb-0">Enable reminders first.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="text-muted">How it works</h6>
                    <ul class="small text-muted mb-0">
                        <li>Only students with a phone number on file are contacted.</li>
                        <li>Messages are logged under <a href="<?php echo e(route('sms.logs')); ?>">SMS Logs</a>.</li>
                        <li>Manual bulk SMS from Fee Summary still works independently.</li>
                        <li>Ensure <code>php artisan schedule:run</code> runs every minute on your server.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reminders/settings.blade.php ENDPATH**/ ?>