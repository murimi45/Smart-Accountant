@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Expense Categories</h4>
                <p class="text-muted mb-0">Manage expense categories for financial tracking</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fa fa-plus me-2"></i>Add Category
                </button>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-circle me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Categories Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-list me-2"></i>All Categories
                </h5>
                <span class="badge bg-light text-dark">{{ count($categories) }} Categories</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table category-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <span class="row-number">#{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon">
                                            {{ strtoupper(substr($category->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <div class="category-name">{{ $category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($category->description)
                                        <span class="description-text">{{ Str::limit($category->description, 50) }}</span>
                                    @else
                                        <span class="text-muted description-empty">
                                            <i class="fa fa-minus me-1"></i>No description
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-light" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editCategoryModal{{ $category->id }}"
                                                title="Edit Category">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <form action="{{ route('expense_categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this category?')"
                                                    title="Delete Category">
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
                                        <p class="mb-2">No categories found</p>
                                        <small class="d-block mb-3">Click "Add Category" to create one</small>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                            <i class="fa fa-plus me-2"></i>Add Category
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

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('expense_categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">
                        <i class="fa fa-plus-circle me-2"></i>Add Expense Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="add_name"
                               name="name" 
                               class="form-control" 
                               placeholder="e.g., Transport, Utilities, Salaries"
                               required>
                    </div>
                    <div class="mb-0">
                        <label for="add_description" class="form-label">
                            Description
                        </label>
                        <textarea id="add_description"
                                  name="description" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Enter a brief description (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-1"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modals --}}
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('expense_categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-edit me-2"></i>Edit Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_{{ $category->id }}" class="form-label">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="edit_name_{{ $category->id }}"
                               name="name" 
                               class="form-control" 
                               value="{{ $category->name }}"
                               required>
                    </div>
                    <div class="mb-0">
                        <label for="edit_description_{{ $category->id }}" class="form-label">
                            Description
                        </label>
                        <textarea id="edit_description_{{ $category->id }}"
                                  name="description" 
                                  class="form-control" 
                                  rows="4">{{ $category->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
/* Base Variables */
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

/* Table */
.category-table {
    font-size: 14px;
}

.category-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.category-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.category-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.category-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Row Number */
.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

/* Category Icon */
.category-icon {
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

.category-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Description */
.description-text {
    color: var(--gray-700);
    font-size: 13px;
}

.description-empty {
    font-size: 13px;
    font-style: italic;
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
textarea.form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus,
textarea.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

textarea.form-control {
    resize: vertical;
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .category-table {
        font-size: 13px;
    }

    .category-table thead th,
    .category-table tbody td {
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

@endsection