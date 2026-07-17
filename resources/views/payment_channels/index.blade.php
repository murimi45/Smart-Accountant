@extends('layouts.app')

@section('main')
<div class="main-wrapper">

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Payment Channels</h4>
                <p class="text-muted mb-0">Manage and configure your active school payment options</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addChannelModal">
                    <i class="fa fa-plus me-2"></i>Add Channel
                </button>
            </div>
        </div>
    </div>

    {{-- Payment Channels Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-credit-card me-2"></i>Channel List
                </h5>
                <span class="badge bg-light text-dark">{{ $channels->count() }} Total</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Type</th>
                            <th>Identifier</th>
                            <th>Account Pattern</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($channels as $channel)
                        <tr>
                            <td>
                                <span class="id-text">#{{ $channel->id }}</span>
                            </td>
                            <td>
                                <span class="role-badge">{{ ucfirst($channel->type) }}</span>
                            </td>
                            <td>
                                <span class="admin-name">{{ $channel->identifier }}</span>
                            </td>
                            <td>
                                @if($channel->account_pattern)
                                    <code class="pattern-code">{{ $channel->account_pattern }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($channel->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fa fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fa fa-ban me-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-buttons justify-content-center">
                                    <button 
                                        class="btn btn-sm btn-light editBtn"
                                        data-id="{{ $channel->id }}"
                                        data-type="{{ $channel->type }}"
                                        data-identifier="{{ $channel->identifier }}"
                                        data-pattern="{{ $channel->account_pattern }}"
                                        data-status="{{ $channel->is_active }}"
                                        title="Edit Channel">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    @if($channel->is_active)
                                        <a href="{{ route('payment_channels.deactivate', $channel->id) }}" 
                                           class="btn btn-sm btn-warning-soft"
                                           title="Deactivate Channel">
                                            <i class="fa fa-power-off"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('payment_channels.activate', $channel->id) }}" 
                                           class="btn btn-sm btn-success-soft"
                                           title="Activate Channel">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No payment channels defined yet</p>
                                    <small class="d-block">Channels you add will appear here</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Channel Modal --}}
<div class="modal fade" id="addChannelModal" tabindex="-1" aria-labelledby="addChannelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('payment_channels.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus-circle me-2"></i>Add Payment Channel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="form-group mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="">-- Select Type --</option>
                    <option value="paybill">Paybill</option>
                    <option value="till">Till</option>
                    <option value="send_money">Send Money</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Identifier</label>
                <input type="text" name="identifier" class="form-control" required placeholder="Shortcode or Phone Number">
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Account Pattern</label>
                <select name="account_pattern" class="form-select">
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
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save me-2"></i>Save Channel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit Channel Modal --}}
<div class="modal fade" id="editChannelModal" tabindex="-1" aria-labelledby="editChannelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editChannelForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Payment Channel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="form-group mb-3">
                <label class="form-label">Type</label>
                <select name="type" id="editType" class="form-select" required>
                    <option value="paybill">Paybill</option>
                    <option value="till">Till</option>
                    <option value="send_money">Send Money</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Identifier</label>
                <input type="text" id="editIdentifier" name="identifier" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Account Pattern (Optional)</label>
                <input type="text" id="editPattern" name="account_pattern" class="form-control">
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Status</label>
                <select name="is_active" id="editStatus" class="form-select" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-sync me-2"></i>Update Channel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit Script --}}
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

/* Card */
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

/* Table */
.admin-table {
    font-size: 14px;
}

.admin-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.admin-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.admin-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.admin-table tbody tr:hover {
    background-color: var(--gray-50);
}

.id-text {
    font-weight: 600;
    color: var(--gray-500);
    font-size: 13px;
}

.admin-name {
    font-weight: 500;
    color: var(--gray-900);
}

.pattern-code {
    background: var(--gray-100);
    color: var(--gray-600);
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
}

/* Role Badge — flat, matches admin/accountant role badge */
.role-badge {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Status Badges — flat colors matching palette */
.status-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

.status-active {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.status-inactive {
    background-color: var(--gray-100);
    color: var(--gray-500);
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

.btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
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

.btn-light {
    background-color: var(--gray-100);
    border-color: var(--gray-200);
    color: var(--gray-700);
}

.btn-light:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
}

/* Soft action buttons for activate/deactivate */
.btn-success-soft {
    background-color: #e8f5e0;
    border-color: #e8f5e0;
    color: #3d7a1f;
}

.btn-success-soft:hover {
    background-color: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.btn-warning-soft {
    background-color: #fef3c7;
    border-color: #fef3c7;
    color: #92400e;
}

.btn-warning-soft:hover {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: white;
}

/* Action Buttons row */
.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Empty State */
.empty-state {
    color: var(--gray-400);
}

.empty-state i {
    opacity: 0.3;
}

.empty-state p {
    font-size: 16px;
    font-weight: 500;
    color: var(--gray-600);
}

.empty-state small {
    color: var(--gray-500);
}

/* Modal styling — flat header matching palette instead of gradient */
.modal-content {
    border: none;
    border-radius: var(--border-radius);
}

.modal-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.modal-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.modal-body {
    padding: 20px 24px;
}

.modal-footer {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 16px 24px;
}

.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control,
.form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    background-color: white;
    transition: border-color 0.2s;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .action-buttons {
        flex-wrap: wrap;
    }
    .admin-table {
        font-size: 13px;
    }
    .admin-table thead th,
    .admin-table tbody td {
        padding: 10px;
    }
}
</style>

@endsection