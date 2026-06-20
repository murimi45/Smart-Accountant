<?php
    $hasFlash = session('success') || session('error') || session('warning') || session('info') || session('payment-error');
?>

<?php if($hasFlash): ?>
<div class="flash-messages">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error') || session('payment-error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i><?php echo e(session('error') ?? session('payment-error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i><?php echo e(session('warning')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('info')): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-info-circle me-2"></i><?php echo e(session('info')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/partials/flash-messages.blade.php ENDPATH**/ ?>