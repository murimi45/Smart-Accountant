@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">{{ $run->period->format('F Y') }}</h4>
                <p class="text-muted mb-0">{{ ucfirst($run->status) }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('payroll.runs.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Headcount</div><strong>{{ $totals['headcount'] }}</strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Gross</div><strong>KES {{ number_format($totals['gross'], 2) }}</strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Deductions</div><strong>KES {{ number_format($totals['deductions'], 2) }}</strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Net pay</div><strong>KES {{ number_format($totals['net_pay'], 2) }}</strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Employer cost</div><strong>KES {{ number_format($totals['employer_cost'], 2) }}</strong></div></div></div>
    </div>

    @unless($run->isLocked())
        <form action="{{ route('payroll.runs.recalculate', $run->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-secondary mb-3">Recalculate</button>
        </form>
        <form action="{{ route('payroll.runs.lock', $run->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success mb-3" onclick="return confirm('Lock this month? Payslips cannot be changed after this.')">Lock month</button>
        </form>
    @endunless

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th>Gross</th>
                        <th>PAYE</th>
                        <th>Net</th>
                        <th>Lines</th>
                        @unless($run->isLocked())
                            <th>This month</th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @foreach($payslips as $payslip)
                        <tr @class(['text-muted' => ! $payslip->included])>
                            <td>
                                {{ $payslip->full_name }}
                                <div class="small text-muted">{{ $payslip->staff_number }}</div>
                            </td>
                            <td>{{ number_format($payslip->gross, 2) }}</td>
                            <td>{{ number_format($payslip->paye, 2) }}</td>
                            <td>{{ number_format($payslip->net_pay, 2) }}</td>
                            <td>
                                <details>
                                    <summary>View</summary>
                                    <div class="small mt-2">
                                        <div>Taxable pay: KES {{ number_format($payslip->taxable_pay, 2) }}</div>
                                        @foreach($payslip->lines as $line)
                                            <div>{{ $line->name }}: KES {{ number_format($line->amount, 2) }}</div>
                                        @endforeach
                                    </div>
                                </details>
                            </td>
                            @unless($run->isLocked())
                                <td>
                                    <form action="{{ route('payroll.payslips.update', [$run->id, $payslip->id]) }}" method="POST" class="d-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="included" value="0">
                                        <label class="small mb-0">
                                            <input type="checkbox" name="included" value="1" @checked($payslip->included)> Include
                                        </label>
                                        <input type="number" name="adjustment" class="form-control form-control-sm" step="0.01" value="{{ $payslip->adjustment }}" style="width: 110px" title="One-off amount added to gross">
                                        <button class="btn btn-sm btn-success">Save</button>
                                    </form>
                                </td>
                            @endunless
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
