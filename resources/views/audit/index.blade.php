@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Finance audit log</h4>
        <p class="text-muted mb-0">Who changed fees, recorded payments, reversed payments, and voided invoices</p>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Action type</label>
                    <select name="event" class="form-select form-select-sm">
                        <option value="">All actions</option>
                        @foreach($eventLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('event') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->admin_name ?? $user->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}"
                           placeholder="Student, admission, amount…">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-history me-2"></i>Audit trail</h5>
            <span class="badge bg-light text-dark">{{ $logs->total() }} entries</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>When</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $props = $log->properties instanceof \Illuminate\Support\Collection
                                    ? $log->properties->toArray()
                                    : (array) ($log->properties ?? []);
                            @endphp
                            <tr>
                                <td class="text-nowrap small">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="small">
                                    @if($log->causer)
                                        {{ $log->causer->admin_name ?? $log->causer->email }}
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td class="small">
                                    <span class="badge bg-secondary-subtle text-dark">
                                        {{ $eventLabels[$log->event] ?? ($log->event ?? 'Action') }}
                                    </span>
                                </td>
                                <td class="small">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No audit entries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
