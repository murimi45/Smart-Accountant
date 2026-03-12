
<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Accountant Management</h4>
                <p class="text-muted mb-0">Manage all accountants in the school</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('accountants.create')); ?>" class="btn btn-primary">
                    <i class="fa fa-plus me-2"></i>Add New Accountant
                </a>
            </div>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('accountants.index')); ?>" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-user me-1"></i>Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" value="<?php echo e(request('name')); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-envelope me-1"></i>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter email" value="<?php echo e(request('email')); ?>">
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-search me-1"></i>Search
                            </button>
                            <a href="<?php echo e(route('accountants.index')); ?>" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-users me-2"></i>Accountant List
                </h5>
                <span class="badge bg-light text-dark"><?php echo e($accountants->total()); ?> Accountants</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table student-table mb-0">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $accountants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accountant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        
                        <tr>
                           
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="student-avatar">
                                        <?php echo e(strtoupper(substr($accountant->admin_name, 0, 1))); ?>

                                    </div>
                                    <div class="ms-3">
                                        <div class="student-name"><?php echo e($accountant->admin_name); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo e($accountant->email); ?></td>
                            <td><?php echo e($accountant->phone ?? 'N/A'); ?></td>
                            <td><?php echo e(ucfirst($accountant->role)); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('accountants.edit', $accountant->id)); ?>" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Accountant">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('accountants.destroy', $accountant->id)); ?>" method="POST" style="display:inline-block">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this accountant?')" 
                                                title="Delete Accountant">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No accountants found</p>
                                    <small class="d-block mb-3">Try adjusting your search filters</small>
                                    <a href="<?php echo e(route('accountants.create')); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus me-2"></i>Add New Accountant
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <?php if($accountants->hasPages()): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing <?php echo e($accountants->firstItem()); ?> to <?php echo e($accountants->lastItem()); ?> of <?php echo e($accountants->total()); ?> entries
                    </small>
                </div>
                <div>
                    <?php echo e($accountants->links()); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/accountant/index.blade.php ENDPATH**/ ?>