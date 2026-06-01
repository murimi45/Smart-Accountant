@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Accountant Management</h4>
                <p class="text-muted mb-0">Manage all accountants in the school</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('accountants.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus me-2"></i>Add New Accountant
                </a>
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

    {{-- Search Filters Card --}}
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('accountants.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-user me-1"></i>Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" value="{{ request('name') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-envelope me-1"></i>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter email" value="{{ request('email') }}">
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-search me-1"></i>Search
                            </button>
                            <a href="{{ route('accountants.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Accountant List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-users me-2"></i>Accountant List
                </h5>
                <span class="badge bg-light text-dark">{{ $accountants->total() }} Accountants</span>
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
                        @forelse($accountants as $accountant)
                        
                        <tr>
                           
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($accountant->admin_name, 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="student-name">{{ $accountant->admin_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $accountant->email }}</td>
                            <td>{{ $accountant->phone ?? 'N/A' }}</td>
                            <td>{{ ucfirst($accountant->role) }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('accountants.edit', $accountant->id) }}" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Accountant">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('accountants.destroy', $accountant->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this accountant?')" 
                                                title="Delete Accountant">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No accountants found</p>
                                    <small class="d-block mb-3">Try adjusting your search filters</small>
                                    <a href="{{ route('accountants.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus me-2"></i>Add New Accountant
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($accountants->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing {{ $accountants->firstItem() }} to {{ $accountants->lastItem() }} of {{ $accountants->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $accountants->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
