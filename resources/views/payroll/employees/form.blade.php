@extends('layouts.app')

@section('main')
@php $editing = $employee->exists; @endphp
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">{{ $editing ? 'Edit Employee' : 'Add Employee' }}</h4>
                <p class="text-muted mb-0">Identity, statutory numbers, and current grade</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

    <div class="card">
        <div class="card-body">
            <form action="{{ $editing ? route('employees.update', $employee->id) : route('employees.store') }}" method="POST">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Staff number <span class="text-danger">*</span></label>
                        <input type="text" name="staff_number" class="form-control" required value="{{ old('staff_number', $employee->staff_number) }}">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Full name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required value="{{ old('full_name', $employee->full_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['active' => 'Active', 'left' => 'Left'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $employee->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($employee->start_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Salary grade</label>
                        <select name="salary_grade_id" class="form-select">
                            <option value="">No grade yet</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" @selected((string) old('salary_grade_id', $employee->salary_grade_id) === (string) $grade->id)>
                                    {{ $grade->name }} — KES {{ number_format($grade->basic_pay, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">KRA PIN</label>
                        <input type="text" name="kra_pin" class="form-control" value="{{ old('kra_pin', $employee->kra_pin) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">NSSF number</label>
                        <input type="text" name="nssf_number" class="form-control" value="{{ old('nssf_number', $employee->nssf_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">SHIF number</label>
                        <input type="text" name="shif_number" class="form-control" value="{{ old('shif_number', $employee->shif_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pay method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Not set</option>
                            @foreach(['bank' => 'Bank', 'mpesa' => 'M-Pesa', 'cash' => 'Cash'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method', $employee->payment_method) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Account or M-Pesa number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $employee->account_number) }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">{{ $editing ? 'Update Employee' : 'Save Employee' }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($editing)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Deductions</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Recurring deductions repeat every month. A balance charges the installment until the remaining amount is used up. These come off after PAYE.</p>

                @forelse($employee->deductions as $deduction)
                    <form action="{{ route('employees.deductions.update', [$employee->id, $deduction->id]) }}" method="POST" class="border rounded p-3 mb-3">
                        @csrf
                        @method('PUT')
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ $deduction->name }}" @disabled(! $deduction->is_active)>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Kind</label>
                                <select name="kind" class="form-select" @disabled(! $deduction->is_active)>
                                    <option value="recurring" @selected($deduction->kind === 'recurring')>Recurring</option>
                                    <option value="balance" @selected($deduction->kind === 'balance')>Balance</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Monthly amount</label>
                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required value="{{ $deduction->amount }}" @disabled(! $deduction->is_active)>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Balance left</label>
                                <input type="number" name="balance_remaining" class="form-control" min="0.01" step="0.01" value="{{ $deduction->balance_remaining }}" @disabled(! $deduction->is_active)>
                            </div>
                            <div class="col-md-3 mb-2 text-md-end">
                                @if($deduction->is_active)
                                    <button type="submit" class="btn btn-success">Save</button>
                                @else
                                    <span class="text-muted">Stopped</span>
                                @endif
                            </div>
                        </div>
                    </form>
                    @if($deduction->is_active)
                        <form action="{{ route('employees.deductions.stop', [$employee->id, $deduction->id]) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Stop this deduction?')">Stop</button>
                        </form>
                    @endif
                @empty
                    <p class="text-muted">No deductions yet.</p>
                @endforelse

                <hr>
                <h6 class="mb-3">Add a deduction</h6>
                <form action="{{ route('employees.deductions.store', $employee->id) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="SACCO, advance, HELB">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Kind</label>
                            <select name="kind" class="form-select" required>
                                <option value="recurring">Recurring</option>
                                <option value="balance">Balance</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Monthly amount</label>
                            <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Balance left</label>
                            <input type="number" name="balance_remaining" class="form-control" min="0.01" step="0.01" placeholder="Balance only">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection