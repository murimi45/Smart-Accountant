

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">

    
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Payment Channels</h4>
                <p class="text-muted mb-0 mt-1">Manage and configure your active school payment options</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-primary fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addChannelModal">
                    <i class="fa fa-plus me-2"></i> Add Channel
                </button>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="card-header">
                    <h5>
                        <i class="fa fa-credit-card me-2" style="color: #36a9e2;"></i>Channel List
                        <span class="badge bg-primary ms-2" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">
                            <?php echo e($channels->count()); ?> Total
                        </span>
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 70px;"><i class="fa fa-hashtag me-2"></i>ID</th>
                                    <th><i class="fa fa-tag me-2"></i>Type</th>
                                    <th><i class="fa fa-id-card me-2"></i>Identifier</th>
                                    <th><i class="fa fa-list-alt me-2"></i>Account Pattern</th>
                                    <th><i class="fa fa-toggle-on me-2"></i>Status</th>
                                    <th class="text-center"><i class="fa fa-cogs me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-muted">#<?php echo e($channel->id); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); color: white; padding: 8px 14px; border-radius: 6px; font-weight: 600;">
                                            <?php echo e(ucfirst($channel->type)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-weight: 500; color: #2c3e50;"><?php echo e($channel->identifier); ?></span>
                                    </td>
                                    <td>
                                        <?php if($channel->account_pattern): ?>
                                            <code style="background: #f8f9fa; color: #555; border-radius: 6px; padding: 5px 8px;"><?php echo e($channel->account_pattern); ?></code>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($channel->is_active): ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #79c347 0%, #5fa732 100%); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                <i class="fa fa-check-circle me-1"></i> Active
                                            </span>
                                        <?php else: ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                <i class="fa fa-ban me-1"></i> Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button 
                                                class="btn btn-sm btn-outline-primary editBtn"
                                                data-id="<?php echo e($channel->id); ?>"
                                                data-type="<?php echo e($channel->type); ?>"
                                                data-identifier="<?php echo e($channel->identifier); ?>"
                                                data-pattern="<?php echo e($channel->account_pattern); ?>"
                                                data-status="<?php echo e($channel->is_active); ?>">
                                                <i class="fa fa-edit me-1"></i>Edit
                                            </button>
                                            <?php if($channel->is_active): ?>
                                                <a href="<?php echo e(route('payment_channels.deactivate', $channel->id)); ?>" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="fa fa-power-off me-1"></i>Deactivate
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('payment_channels.activate', $channel->id)); ?>" 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="fa fa-check me-1"></i>Activate
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div style="color: #9ca3af;">
                                            <i class="fa fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                            <p class="mb-0">No payment channels defined yet</p>
                                            <small>Channels you add will appear here</small>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addChannelModal" tabindex="-1" aria-labelledby="addChannelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
      <form action="<?php echo e(route('payment_channels.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-header" style="background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); color: white; border-radius: 16px 16px 0 0;">
          <h5 class="modal-title fw-bold"><i class="fa fa-plus-circle me-2"></i>Add Payment Channel</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body px-4 py-3">
            <div class="form-group mb-3">
                <label class="fw-semibold">Type</label>
                <select name="type" class="form-select border-0 shadow-sm bg-light" required>
                    <option value="">-- Select Type --</option>
                    <option value="paybill">Paybill</option>
                    <option value="till">Till</option>
                    <option value="send_money">Send Money</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="fw-semibold">Identifier</label>
                <input type="text" name="identifier" class="form-control border-0 shadow-sm bg-light" required placeholder="Shortcode or Phone Number">
            </div>

            <div class="form-group mb-3">
                <label class="fw-semibold">Account Pattern</label>
                <select name="account_pattern" class="form-select border-0 shadow-sm bg-light">
                    <option value="">-- Select Pattern --</option>
                    <option value="{name}-{class}-{admission_no}">Name-Class-Admission</option>
                    <option value="{class}-{admission_no}">Class-Admission</option>
                    <option value="{admission_no}">Admission Only</option>
                </select>
                <small class="text-muted d-block mt-2">
                    Determines how parents must enter the account/reference field during payment.
                </small>
            </div>
        </div>
        <div class="modal-footer d-flex justify-content-between align-items-center px-4 py-3" style="background: #fafbfc; border-top: 1px solid #eaecef;">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">
            <i class="fa fa-save me-2"></i>Save Channel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="editChannelModal" tabindex="-1" aria-labelledby="editChannelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
      <form id="editChannelForm" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="modal-header" style="background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%); color: white; border-radius: 16px 16px 0 0;">
          <h5 class="modal-title fw-bold"><i class="fa fa-edit me-2"></i>Edit Payment Channel</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body px-4 py-3">
            <div class="form-group mb-3">
                <label class="fw-semibold">Type</label>
                <select name="type" id="editType" class="form-select border-0 shadow-sm bg-light" required>
                    <option value="paybill">Paybill</option>
                    <option value="till">Till</option>
                    <option value="send_money">Send Money</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="fw-semibold">Identifier</label>
                <input type="text" id="editIdentifier" name="identifier" class="form-control border-0 shadow-sm bg-light" required>
            </div>

            <div class="form-group mb-3">
                <label class="fw-semibold">Account Pattern (Optional)</label>
                <input type="text" id="editPattern" name="account_pattern" class="form-control border-0 shadow-sm bg-light">
            </div>

            <div class="form-group mb-3">
                <label class="fw-semibold">Status</label>
                <select name="is_active" id="editStatus" class="form-select border-0 shadow-sm bg-light" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="modal-footer d-flex justify-content-between align-items-center px-4 py-3" style="background: #fafbfc; border-top: 1px solid #eaecef;">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">
            <i class="fa fa-sync me-2"></i>Update Channel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const type = this.dataset.type;
        const identifier = this.dataset.identifier;
        const pattern = this.dataset.pattern;
        const status = this.dataset.status;

        document.getElementById('editType').value = type;
        document.getElementById('editIdentifier').value = identifier;
        document.getElementById('editPattern').value = pattern || '';
        document.getElementById('editStatus').value = status;
        document.getElementById('editChannelForm').action = '/payment_channels/' + id;

        new bootstrap.Modal(document.getElementById('editChannelModal')).show();
    });
});
</script>


<style>
.btn-primary {
    background: linear-gradient(135deg, #36a9e2 0%, #1e88c7 100%);
    border: none;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(54, 169, 226, 0.3);
}
.btn-outline-primary:hover {
    background: #36a9e2;
    color: #fff;
    border-color: #36a9e2;
}
.btn-outline-success:hover {
    background: #5fa732;
    color: #fff;
}
.btn-outline-warning:hover {
    background: #ff4748;
    color: #fff;
}
.modal-content {
    transition: all 0.3s ease-in-out;
}
.modal-content:hover {
    transform: scale(1.01);
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        gap: 5px;
    }
    .table {
        font-size: 13px;
    }
}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/payment_channels/index.blade.php ENDPATH**/ ?>