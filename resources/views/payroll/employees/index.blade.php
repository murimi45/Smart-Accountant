@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Employees</h4>
                <p class="text-muted mb-0">Staff paid through payroll</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('bulk.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fa fa-upload me-2"></i>Import CSV
                </a>
                <a href="{{ route('employees.create') }}" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add Employee
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Staff no.</th>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->staff_number }}</td>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->grade->name ?? '—' }}</td>
                                <td>{{ $employee->phone ?: '—' }}</td>
                                <td>{{ ucfirst($employee->status) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-light">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No employees yet. Add one, or import a CSV from Bulk Import / Export.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->hasPages())
            <div class="card-footer">{{ $employees->links() }}</div>
        @endif
    </div>
</div>
@endsection