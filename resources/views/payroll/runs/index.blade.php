@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Payroll Months</h4>
        <p class="text-muted mb-0">Prepare a month from active employees, then lock it</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('payroll.runs.store') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <input type="month" name="period" class="form-control" required value="{{ now()->format('Y-m') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">Prepare month</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)
                        <tr>
                            <td>{{ $run->period->format('F Y') }}</td>
                            <td>{{ ucfirst($run->status) }}</td>
                            <td class="text-end">
                                <a href="{{ route('payroll.runs.show', $run->id) }}" class="btn btn-sm btn-light">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No payroll months yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($runs->hasPages())
            <div class="card-footer">{{ $runs->links() }}</div>
        @endif
    </div>
</div>
@endsection
