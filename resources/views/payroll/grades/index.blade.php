@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Salary Grades</h4>
                <p class="text-muted mb-0">Basic pay and standing allowances for each grade</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="fa fa-plus me-2"></i>Add Grade
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Basic pay</th>
                            <th>Allowances</th>
                            <th>Employees</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $grade)
                            <tr>
                                <td>{{ $grade->name }}</td>
                                <td>KES {{ number_format($grade->basic_pay, 2) }}</td>
                                <td>
                                    @forelse($grade->allowances as $allowance)
                                        <div>{{ $allowance->name }}: KES {{ number_format($allowance->amount, 2) }}</div>
                                    @empty
                                        <span class="text-muted">None</span>
                                    @endforelse
                                </td>
                                <td>{{ $grade->employees_count }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editGradeModal{{ $grade->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    @if($grade->employees_count === 0)
                                        <form action="{{ route('salary_grades.destroy', $grade->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this grade?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <div class="modal fade" id="editGradeModal{{ $grade->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('salary_grades.update', $grade->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit {{ $grade->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @include('payroll.grades.fields', ['grade' => $grade])
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-success">Save grade</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No salary grades yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($grades->hasPages())
            <div class="card-footer">{{ $grades->links() }}</div>
        @endif
    </div>
</div>

<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('salary_grades.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('payroll.grades.fields', ['grade' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save grade</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection