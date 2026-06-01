<div class="notifications-card" wire:poll.60s>
    <h5><i class="fa fa-bell me-2"></i> Recent Notifications</h5>

    
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $overdue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="notification-item">
            <div class="notification-icon warning">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div class="notification-content">
                <h6><?php echo e($item->data['title'] ?? 'Payment Overdue'); ?></h6>
                <p><?php echo e($item->data['message'] ?? ''); ?></p>
                <small><i class="fa fa-clock"></i> <?php echo e($item->created_at->diffForHumans()); ?></small>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $income; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="notification-item">
            <div class="notification-icon success">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="notification-content">
                <h6><?php echo e($item->data['title'] ?? 'Payment Received'); ?></h6>
                <p><?php echo e($item->data['message'] ?? ''); ?></p>
                <small><i class="fa fa-clock"></i> <?php echo e($item->created_at->diffForHumans()); ?></small>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $other_income; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="notification-item">
        <div class="notification-icon success">
            <i class="fa fa-check-circle"></i>
        </div>
        <div class="notification-content">
            <h6><?php echo e($item->data['title'] ?? 'Other Income Recorded'); ?></h6>
            <p><?php echo e($item->data['message'] ?? ''); ?></p>
            <small><i class="fa fa-clock"></i> <?php echo e($item->created_at->diffForHumans()); ?></small>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->


    
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $expense; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="notification-item">
            <div class="notification-icon info">
                <i class="fa fa-info-circle"></i>
            </div>
            <div class="notification-content">
                <h6><?php echo e($item->data['title'] ?? 'Expense Recorded'); ?></h6>
                <p><?php echo e($item->data['message'] ?? ''); ?></p>
                <small><i class="fa fa-clock"></i> <?php echo e($item->created_at->diffForHumans()); ?></small>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="notification-item">
            <div class="notification-icon warning">
                <i class="fa fa-bell"></i>
            </div>
            <div class="notification-content">
                <h6><?php echo e($item->data['title'] ?? 'Daily Summary'); ?></h6>
                <p><?php echo e($item->data['message'] ?? ''); ?></p>
                <small><i class="fa fa-clock"></i> <?php echo e($item->created_at->diffForHumans()); ?></small>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if(
        $overdue->isEmpty() &&
        $income->isEmpty() &&
        $expense->isEmpty() &&
        $summary->isEmpty()
    ): ?>
        <div class="notification-item">
            <div class="notification-icon info">
                <i class="fa fa-info-circle"></i>
            </div>
            <div class="notification-content">
                <h6>No new notifications</h6>
                <p>You're all caught up — no unread notifications.</p>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <button class="btn btn-sm btn-primary mt-3" wire:click="markAllAsRead">
        Mark All as Read
    </button>
</div>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/livewire/dashboard-notifications.blade.php ENDPATH**/ ?>