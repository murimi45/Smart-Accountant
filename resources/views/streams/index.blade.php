@extends('layouts.app')

@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Stream Management</h4>
                <p class="text-muted mb-0">Manage all streams under different grades</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStreamModal">
                    <i class="fa fa-plus me-2"></i>Add New Stream
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

    {{-- Streams Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fa fa-stream me-2"></i>Stream List
                    </h5>
                    <small class="text-muted">Manage streams for each grade level</small>
                </div>
                <span class="badge bg-light text-dark">{{ count($streams) }} Streams</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table grade-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Grade</th>
                            <th>Stream Name</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($streams as $stream)
                        <tr>
                            <td>
                                <span class="row-number">#{{ $stream->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="grade-icon">
                                        {{ strtoupper(substr($stream->class->name ?? 'N/A', 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="grade-name">{{ $stream->class->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="stream-name">{{ $stream->name }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button 
                                        class="btn btn-sm btn-light editStreamBtn"
                                        data-id="{{ $stream->id }}"
                                        data-grade-id="{{ $stream->grade_id }}"
                                        data-name="{{ $stream->name }}"
                                        title="Edit Stream">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('streams.destroy', $stream->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this stream?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Stream">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No streams found</p>
                                    <small class="d-block mb-3">Click "Add New Stream" to create one</small>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStreamModal">
                                        <i class="fa fa-plus me-2"></i>Add New Stream
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

{{-- Add Stream Modal --}}
<div class="modal fade" id="addStreamModal" tabindex="-1" aria-labelledby="addStreamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('streams.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addStreamModalLabel">
                        <i class="fa fa-plus me-2"></i>Add New Stream
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="grade_id" class="form-label">Grade <span class="text-danger">*</span></label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Stream Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control" 
                               placeholder="e.g. A, B, C, Red, Blue" 
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-1"></i>Save Stream
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Stream Modal --}}
<div class="modal fade" id="editStreamModal" tabindex="-1" aria-labelledby="editStreamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editStreamForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editStreamModalLabel">
                        <i class="fa fa-edit me-2"></i>Edit Stream
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_grade_id" class="form-label">Grade <span class="text-danger">*</span></label>
                        <select name="class_id" id="edit_class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Stream Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="edit_name" 
                               class="form-control" 
                               required>
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
/* Reuse the same design system as Grade Management */
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

/* All styles copied from Grade Management */
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

.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

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

.stream-name {
    font-weight: 500;
    color: var(--gray-900);
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Modal Styles */
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
.btn-success:hover { background-color: var(--success-dark); }

.btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }

/* Empty State */
.empty-state {
    color: var(--gray-400);
}

.empty-state i { opacity: 0.3; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Edit Stream Functionality
    const editButtons = document.querySelectorAll('.editStreamBtn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const gradeId = this.getAttribute('data-grade-id');
            const name = this.getAttribute('data-name');

            document.getElementById('editStreamForm').action = `{{ url('streams') }}/${id}`;

            document.getElementById('edit_grade_id').value = gradeId;
            document.getElementById('edit_name').value = name || '';

            const editModal = new bootstrap.Modal(document.getElementById('editStreamModal'));
            editModal.show();
        });
    });
});
</script>

@endsection