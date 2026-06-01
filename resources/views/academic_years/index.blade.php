@extends('layouts.app')

@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Academic Year Management</h4>
                <p class="text-muted mb-0">Manage academic years for your school</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addYearModal">
                    <i class="fa fa-plus me-2"></i>Add New Year
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

    {{-- Academic Years Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fa fa-calendar-alt me-2"></i>Academic Year List
                    </h5>
                    <small class="text-muted">Manage school academic years and current status</small>
                </div>
                <span class="badge bg-light text-dark">{{ count($years) }} Years</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table term-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Year</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($years as $year)
                            <tr>
                                <td>
                                    <span class="row-number">#{{ $year->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="term-icon">
                                            {{ strtoupper(substr($year->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <div class="term-name">{{ $year->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="date-text">
                                        <i class="fa fa-calendar-day me-1"></i>
                                        {{ \Carbon\Carbon::parse($year->start_date)->format('d M, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="date-text">
                                        <i class="fa fa-calendar-check me-1"></i>
                                        {{ \Carbon\Carbon::parse($year->end_date)->format('d M, Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($year->is_current)
                                        <span class="badge badge-success">
                                            <i class="fa fa-check-circle me-1"></i>Current
                                        </span>
                                    @else
                                        <span class="badge-method">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button 
                                            class="btn btn-sm btn-light editYearBtn"
                                            data-id="{{ $year->id }}"
                                            data-name="{{ $year->name }}"
                                            data-start="{{ $year->start_date }}"
                                            data-end="{{ $year->end_date }}"
                                            data-current="{{ $year->is_current ? '1' : '0' }}"
                                            title="Edit Academic Year">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('academic-years.destroy', $year->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this year?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Academic Year">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-2">No academic years available</p>
                                        <small class="d-block mb-3">Click "Add New Year" to create one</small>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addYearModal">
                                            <i class="fa fa-plus me-2"></i>Add New Year
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

{{-- Add Academic Year Modal --}}
<div class="modal fade" id="addYearModal" tabindex="-1" aria-labelledby="addYearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addYearForm" action="{{ route('academic-years.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addYearModalLabel">
                        <i class="fa fa-plus me-2"></i>Add New Academic Year
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control" 
                               placeholder="e.g. 2025-2026" 
                               required>
                        <small class="text-muted">Example: 2025-2026 or 2025</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date" 
                                   class="form-control" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date" 
                                   class="form-control" 
                                   required>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_current" id="is_current" value="1">
                        <label class="form-check-label" for="is_current">
                            Set as Current Academic Year
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-1"></i>Save Academic Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Academic Year Modal --}}
<div class="modal fade" id="editYearModal" tabindex="-1" aria-labelledby="editYearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editYearForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editYearModalLabel">
                        <i class="fa fa-edit me-2"></i>Edit Academic Year
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="edit_name" 
                               class="form-control" 
                               required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="start_date" 
                                   id="edit_start_date" 
                                   class="form-control" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="end_date" 
                                   id="edit_end_date" 
                                   class="form-control" 
                                   required>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_current" id="edit_is_current" value="1">
                        <label class="form-check-label" for="edit_is_current">
                            Set as Current Academic Year
                        </label>
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

<style>
/* Reuse all styles from your reference + small additions */
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
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

/* All your existing styles from the reference (Grade Management) are kept here */
.page-header h4 { font-size: 24px; font-weight: 600; color: var(--gray-900); }
.page-header p { font-size: 14px; color: var(--gray-500); }

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
}

.term-table {
    font-size: 14px;
}

.term-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.term-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.term-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.term-table tbody tr:hover {
    background-color: var(--gray-50);
}

.row-number { color: var(--gray-500); font-weight: 500; font-size: 13px; }

.term-icon {
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

.term-name { font-weight: 500; color: var(--gray-900); }

.date-text { color: var(--gray-600); font-size: 13px; }
.date-text i { color: var(--gray-400); }

.action-buttons { display: flex; gap: 6px; }
.action-buttons .btn-sm { padding: 6px 12px; font-size: 13px; }

/* Modal & Form Styles - Same as reference */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.modal-header, .modal-footer {
    padding: 16px 20px;
}

.modal-body { padding: 20px; }

.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control, .form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.btn {
    border-radius: var(--border-radius);
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
}

.btn-success { background-color: var(--success-color); border-color: var(--success-color); }
.btn-success:hover { background-color: var(--success-dark); border-color: var(--success-dark); }

.btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }

/* Badges */
.badge-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.badge-method {
    background-color: var(--gray-100);
    color: var(--gray-700);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Edit Year Button Handler
    const editButtons = document.querySelectorAll('.editYearBtn');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const start = this.getAttribute('data-start');
            const end = this.getAttribute('data-end');
            const isCurrent = this.getAttribute('data-current') === '1';

            // Set form action
            document.getElementById('editYearForm').action = `{{ url('academic-years') }}/${id}`;

            // Fill form fields
            document.getElementById('edit_name').value = name || '';
            document.getElementById('edit_start_date').value = start || '';
            document.getElementById('edit_end_date').value = end || '';
            document.getElementById('edit_is_current').checked = isCurrent;

            // Show modal
            const editModal = new bootstrap.Modal(document.getElementById('editYearModal'));
            editModal.show();
        });
    });
});
</script>

@endsection