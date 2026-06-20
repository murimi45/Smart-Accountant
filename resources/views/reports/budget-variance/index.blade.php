@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">Budget Variance Report</h4>
                <p class="text-muted mb-0">Budget vs actual spending by expense category</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('budgets.index', ['term_id' => $termId]) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-edit me-1"></i>Edit Budgets
                </a>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.budget-variance') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Term</label>
                    <select name="term_id" class="form-select">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ (int) $termId === (int) $term->id ? 'selected' : '' }}>
                                {{ $term->name }} - {{ $term->year }}
                                @if($currentTerm && $term->id === $currentTerm->id) (current) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>

    @if($report)
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-content">
                        <div class="summary-value">KSh {{ number_format($report['totals']['budget'], 2) }}</div>
                        <div class="summary-label">Total Budget</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-content">
                        <div class="summary-value">KSh {{ number_format($report['totals']['actual'], 2) }}</div>
                        <div class="summary-label">Total Spent</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-content">
                        <div class="summary-value {{ $report['totals']['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            KSh {{ number_format($report['totals']['variance'], 2) }}
                        </div>
                        <div class="summary-label">Remaining / (Over)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0">{{ $report['term']->name }} {{ $report['term']->year }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Budget</th>
                                <th class="text-end">Actual</th>
                                <th class="text-end">Variance</th>
                                <th class="text-end">Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['rows'] as $row)
                                <tr class="{{ $row['over_budget'] ? 'table-danger' : '' }}">
                                    <td>{{ $row['category']->name }}</td>
                                    <td class="text-end">{{ number_format($row['budget'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['actual'], 2) }}</td>
                                    <td class="text-end {{ $row['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($row['variance'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        @if($row['utilization'] !== null)
                                            {{ $row['utilization'] }}%
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No budgets or expenses recorded for this term.
                                        <a href="{{ route('budgets.index', ['term_id' => $termId]) }}">Set budgets</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($report['rows']) > 0)
                            <tfoot>
                                <tr class="fw-bold">
                                    <td>Totals</td>
                                    <td class="text-end">{{ number_format($report['totals']['budget'], 2) }}</td>
                                    <td class="text-end">{{ number_format($report['totals']['actual'], 2) }}</td>
                                    <td class="text-end">{{ number_format($report['totals']['variance'], 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
