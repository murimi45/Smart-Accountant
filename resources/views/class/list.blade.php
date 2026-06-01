@extends('layouts.app')

@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Grade Management</h4>
                <p class="text-muted mb-0">Manage all class grades in the system</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="fa fa-plus me-2"></i>Add New Grade
                </button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#promoteModal">
                    <i class="fa fa-level-up-alt me-2"></i>Promote Students
                </button>
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Validation Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Grade List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fa fa-list me-2"></i>Grade List
                    </h5>
                    <small class="text-muted">Manage grade levels and progression order</small>
                </div>
                <span class="badge bg-light text-dark">{{ count($getRecord) }} Grades</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table grade-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Grade Name</th>
                            <th style="width: 120px;">Order</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($getRecord as $value)
                        <tr>
                            <td>
                                <span class="row-number">#{{ $value->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="grade-icon">
                                        {{ strtoupper(substr($value->name, 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="grade-name">{{ $value->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">#{{ $value->order }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button 
                                        class="btn btn-sm btn-light editBtn"
                                        data-id="{{ $value->id }}"
                                        data-name="{{ $value->name }}"
                                        data-order="{{ $value->order }}"
                                        title="Edit Grade">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <a href="{{ url('/deleteClass/' . $value->id) }}" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this grade?')"
                                       title="Delete Grade">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No grades found</p>
                                    <small class="d-block mb-3">Click "Add New Grade" to create one</small>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClassModal">
                                        <i class="fa fa-plus me-2"></i>Add New Grade
                                    </button>
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

{{-- Edit Grade Modal --}}
<div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editClassForm" method="POST" action="">
                @csrf
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClassModalLabel">
                        <i class="fa fa-edit me-2"></i>Edit Grade
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">
                            Grade Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="editName" 
                               name="name" 
                               class="form-control" 
                               placeholder="Enter grade name"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="editOrder" class="form-label">
                            Order <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               id="editOrder" 
                               name="order" 
                               class="form-control" 
                               min="1"
                               placeholder="Enter order (e.g., 1, 2, 3)"
                               required>
                        <small class="text-muted">This determines the progression sequence</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add New Grade(s) Modal --}}
<div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addClassForm" action="{{ url('/insertClass') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">
                        <i class="fa fa-plus me-2"></i>Add New Grade(s)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="classInputs">
                        <div class="class-input-group mb-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Grade Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="classes[0][name]" 
                                           class="form-control" 
                                           placeholder="e.g. Grade 1 / Class 1"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Order <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           name="classes[0][order]" 
                                           class="form-control" 
                                           min="1"
                                           placeholder="e.g. 1"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-success btn-sm" id="addMoreInput">
                            <i class="fa fa-plus-circle me-1"></i>Add Another Grade
                        </button>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <small>Tip: Add grades in ascending order (1 → 2 → 3). Order determines student promotion sequence.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-1"></i>Save Grades
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Promote Students Modal (Unchanged) --}}
<div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('promotions.class') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="promoteModalLabel">
                        <i class="fa fa-level-up-alt me-2"></i>Promote Students to Next Grade
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        This will promote all eligible students to the next grade based on <strong>Order</strong>.
                    </p>

                    <div class="mb-3">
                        <label for="academic_year" class="form-label">
                            Target Academic Year <span class="text-danger">*</span>
                        </label>
                        @if(($academicYears ?? collect())->isEmpty())
                            <p class="text-danger mb-0" style="font-size:13px;">
                                No academic year exists after
                                <strong>{{ $currentAcademicYear?->name ?? 'the current year' }}</strong>.
                                Create the next year and its first term first.
                            </p>
                        @else
                            <select name="academic_year" id="academic_year" class="form-select" required>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->name }}"
                                        {{ ($nextAcademicYear && $nextAcademicYear->id === $ay->id) ? 'selected' : '' }}>
                                        {{ $ay->name }}
                                        @if($nextAcademicYear && $nextAcademicYear->id === $ay->id) (next) @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($currentAcademicYear)
                                <small class="text-muted d-block mt-1">
                                    Current year: <strong>{{ $currentAcademicYear->name }}</strong>. Only forward years are allowed.
                                </small>
                            @endif
                        @endif
                    </div>

                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> This process cannot be undone.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" @if(($academicYears ?? collect())->isEmpty()) disabled @endif>
                        <i class="fa fa-check me-1"></i>Confirm Promotion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
/* Base Variables */
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

.table-card .card-header small {
    font-size: 13px;
    color: var(--gray-500);
    display: block;
    margin-top: 2px;
}

/* Table */
.grade-table {
    font-size: 14px;
}

.grade-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.grade-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.grade-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.grade-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Row Number */
.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

/* Grade Icon */
.grade-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: var(--gray-100);
    color: var(--gray-700);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    flex-shrink: 0;
}

.grade-name {
    font-weight: 500;
    color: var(--gray-900);
}

.grade-description {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
}

/* Next Class Badge */
.next-class-badge {
    background-color: #dbeafe;
    color: #1e40af;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
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

.btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
}

.btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
    border-color: #d97706;
    color: white;
}

.btn-secondary {
    background-color: var(--gray-600);
    border-color: var(--gray-600);
}

.btn-secondary:hover {
    background-color: var(--gray-700);
    border-color: var(--gray-700);
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

.btn-outline-success {
    color: var(--success-color);
    border-color: var(--success-color);
}

.btn-outline-success:hover {
    background-color: var(--success-color);
    border-color: var(--success-color);
    color: white;
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

.alert-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.alert-info {
    background-color: #e0f2fe;
    color: #075985;
}

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Modal */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.modal-header h5 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    border-top: 1px solid var(--gray-200);
    padding: 16px 20px;
}

/* Form Elements */
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
    transition: border-color 0.2s;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Class Input Group */
.class-input-group {
    position: relative;
    padding: 16px;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    background-color: var(--gray-50);
}

.remove-input {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 10;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .grade-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .grade-table {
        font-size: 13px;
    }

    .grade-table thead th,
    .grade-table tbody td {
        padding: 10px;
    }

    .modal-body {
        padding: 16px;
    }

    .modal-footer {
        padding: 12px 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addMoreBtn = document.getElementById('addMoreInput');
    let classInputCounter = 1;

    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function() {
            const container = document.getElementById('classInputs');
            if (!container) return;

            const inputGroup = document.createElement('div');
            inputGroup.classList.add('class-input-group', 'mb-3');

            inputGroup.innerHTML = `
                <button type="button" class="btn btn-sm btn-danger remove-input">
                    <i class="fa fa-times"></i>
                </button>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Grade Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="classes[${classInputCounter}][name]"
                               class="form-control"
                               placeholder="e.g. Grade 1 / Class 1"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            Order <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="classes[${classInputCounter}][order]"
                               class="form-control"
                               min="1"
                               placeholder="e.g. ${classInputCounter + 1}"
                               required>
                    </div>
                </div>
            `;

            container.appendChild(inputGroup);
            classInputCounter++;

            const removeBtn = inputGroup.querySelector('.remove-input');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    inputGroup.remove();
                });
            }
        });
    }

    const editButtons = document.querySelectorAll('.editBtn');
    if (editButtons.length) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const order = this.getAttribute('data-order');

                const editForm = document.getElementById('editClassForm');
                if (editForm) {
                    editForm.action = '{{ url("/editClass") }}/' + id;
                }

                const nameInput = document.getElementById('editName');
                if (nameInput) nameInput.value = name || '';

                const orderInput = document.getElementById('editOrder');
                if (orderInput) orderInput.value = order || '';

                const editModalEl = document.getElementById('editClassModal');
                if (editModalEl) {
                    const editModal = new bootstrap.Modal(editModalEl);
                    editModal.show();
                }
            });
        });
    }
});
</script>

@endsection