<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Bulk Import / Export</h4>
        <p class="text-muted mb-0">Download CSV templates, export data, or import students, class fees, and payments</p>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4"><?php echo e(session('warning')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4"><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('import_errors')): ?>
        <div class="alert alert-danger mb-4">
            <strong>Import issues:</strong>
            <ul class="mb-0 mt-2 small">
                <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if($isAdmin): ?>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-users me-2"></i>Students</h5></div>
                <div class="card-body">
                    <p class="text-muted small">Import new students with class & term enrollment, or export all student records.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('bulk.template', 'students')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fa fa-download me-1"></i>Download template</a>
                        <a href="<?php echo e(route('bulk.export', 'students')); ?>" class="btn btn-outline-primary btn-sm"><i class="fa fa-file-export me-1"></i>Export CSV</a>
                    </div>
                    <hr>
                    <form action="<?php echo e(route('bulk.import', 'students')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <label class="form-label small">Import CSV</label>
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-upload me-1"></i>Import students</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-file-invoice-dollar me-2"></i>Fees</h5></div>
                <div class="card-body">
                    <p class="text-muted small">
                        <strong>Export</strong> student invoices (balances). <strong>Import</strong> class fee amounts per class/term (updates invoices automatically).
                    </p>
                    <form method="GET" action="<?php echo e(route('bulk.export', 'fees')); ?>" class="mb-2">
                        <label class="form-label small">Term filter (export)</label>
                        <select name="term_id" class="form-select form-select-sm mb-2">
                            <option value="">All terms</option>
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>"><?php echo e($term->name); ?> - <?php echo e($term->year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa fa-file-export me-1"></i>Export fee invoices</button>
                    </form>
                    <a href="<?php echo e(route('bulk.template', 'fees')); ?>" class="btn btn-outline-secondary btn-sm w-100 mb-2"><i class="fa fa-download me-1"></i>Download class-fee template</a>
                    <form action="<?php echo e(route('bulk.import', 'fees')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-upload me-1"></i>Import class fees</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fa fa-money-bill-wave me-2"></i>Payments</h5></div>
                <div class="card-body">
                    <p class="text-muted small">Bulk record fee payments by admission number, or export payment history.</p>
                    <form method="GET" action="<?php echo e(route('bulk.export', 'payments')); ?>" class="mb-2">
                        <label class="form-label small">Term filter (export)</label>
                        <select name="term_id" class="form-select form-select-sm mb-2">
                            <option value="">All terms</option>
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>"><?php echo e($term->name); ?> - <?php echo e($term->year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa fa-file-export me-1"></i>Export payments</button>
                    </form>
                    <a href="<?php echo e(route('bulk.template', 'payments')); ?>" class="btn btn-outline-secondary btn-sm w-100 mb-2"><i class="fa fa-download me-1"></i>Download template</a>
                    <form action="<?php echo e(route('bulk.import', 'payments')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".csv,text/csv" required>
                        <button type="submit" class="btn btn-success btn-sm w-100"><i class="fa fa-upload me-1"></i>Import payments</button>
                    </form>
                    <p class="text-muted small mt-2 mb-0">Payments apply to the current term unless you include a <code>term</code> column (e.g. <code>Term 1 - 2026</code>).</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h6 class="mb-2">CSV tips</h6>
            <ul class="small text-muted mb-0">
                <li>Use the template headers exactly — open in Excel or Google Sheets, save as CSV UTF-8.</li>
                <li><strong>Class</strong> and <strong>term</strong> must match names in the system (e.g. <code>Grade 1</code>, <code>Term 1 - 2026</code>).</li>
                <li>Duplicate student admissions update the existing record and add enrollment for a new term if needed.</li>
                <li>Payment import uses the same rules as manual payment entry (current term invoice, not voided).</li>
            </ul>
        </div>
    </div>
</div>

<style>
.card { border:1px solid #e5e7eb; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,.05); }
.card-header { background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:14px 18px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/bulk/index.blade.php ENDPATH**/ ?>