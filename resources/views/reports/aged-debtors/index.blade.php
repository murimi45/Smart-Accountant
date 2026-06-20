@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Aged Debtors Report</h4>
        <p class="text-muted mb-0">Outstanding fee balances by age, filtered by class and term</p>
    </div>

    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.aged-debtors') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-school me-1"></i>Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (int) $classId === (int) $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Term</label>
                        <select name="term_id" class="form-select">
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ (int) $termId === (int) $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} - {{ $term->year }}
                                    @if($currentTerm && $term->id === $currentTerm->id) (current) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('reports.aged-debtors') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card actions-card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div>
                    <span class="text-muted me-2"><i class="fa fa-info-circle me-1"></i>As at:</span>
                    <strong>{{ $report['asOf']->format('d M Y') }}</strong>
                    @if($selectedTerm)
                        <span class="text-muted ms-3">Term: <strong>{{ $selectedTerm->name }} {{ $selectedTerm->year }}</strong></span>
                    @endif
                    @if($selectedClass)
                        <span class="text-muted ms-3">Class: <strong>{{ $selectedClass->name }}</strong></span>
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $exportQuery = http_build_query(array_filter([
                            'term_id' => $termId,
                            'class_id' => $classId,
                        ]));
                    @endphp
                    <a href="{{ route('reports.aged-debtors.pdf') }}?{{ $exportQuery }}" class="btn btn-outline-danger btn-action">
                        <i class="fa fa-file-pdf me-1"></i>Export PDF
                    </a>
                    <a href="{{ route('reports.aged-debtors.excel') }}?{{ $exportQuery }}" class="btn btn-outline-success btn-action">
                        <i class="fa fa-file-excel me-1"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #fee2e2;">
                    <i class="fa fa-users" style="color: #ef4444;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">{{ number_format($report['debtorCount']) }}</div>
                    <div class="summary-label">Debtors</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #fef3c7;">
                    <i class="fa fa-money-bill-wave" style="color: #f59e0b;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh {{ number_format($report['totals']['total_balance'], 2) }}</div>
                    <div class="summary-label">Total Outstanding</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #fee2e2;">
                    <i class="fa fa-clock" style="color: #dc2626;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh {{ number_format($report['totals']['days_91_plus'], 2) }}</div>
                    <div class="summary-label">91+ Days Overdue</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #e8f5e0;">
                    <i class="fa fa-check-circle" style="color: #79c347;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh {{ number_format($report['totals']['current'], 2) }}</div>
                    <div class="summary-label">Current (0–30 days)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-table me-2"></i>Aging Detail</h5>
                <span class="badge bg-light text-dark">{{ $report['debtorCount'] }} rows</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table invoice-table mb-0">
                    <thead>
                        <tr>
                            <th>Admission</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Oldest Invoice</th>
                            <th>Days</th>
                            <th class="text-end">Current</th>
                            <th class="text-end">31–60</th>
                            <th class="text-end">61–90</th>
                            <th class="text-end">91+</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['rows'] as $row)
                            <tr>
                                <td>{{ $row['admission'] }}</td>
                                <td>{{ $row['student_name'] }}</td>
                                <td>{{ $row['class_name'] }}</td>
                                <td>{{ $row['term_name'] }}</td>
                                <td>{{ $row['invoice_date'] ? \Carbon\Carbon::parse($row['invoice_date'])->format('d M Y') : '—' }}</td>
                                <td>{{ $row['days_outstanding'] }}</td>
                                <td class="text-end">{{ number_format($row['buckets']['current'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['buckets']['days_31_60'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['buckets']['days_61_90'], 2) }}</td>
                                <td class="text-end {{ $row['buckets']['days_91_plus'] > 0 ? 'text-danger fw-semibold' : '' }}">
                                    {{ number_format($row['buckets']['days_91_plus'], 2) }}
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($row['total_balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    No outstanding balances for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($report['rows']) > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="6" class="text-end">Totals</td>
                                <td class="text-end">{{ number_format($report['totals']['current'], 2) }}</td>
                                <td class="text-end">{{ number_format($report['totals']['days_31_60'], 2) }}</td>
                                <td class="text-end">{{ number_format($report['totals']['days_61_90'], 2) }}</td>
                                <td class="text-end">{{ number_format($report['totals']['days_91_plus'], 2) }}</td>
                                <td class="text-end">{{ number_format($report['totals']['total_balance'], 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.filter-card, .actions-card, .table-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.summary-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.summary-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.summary-icon i { font-size: 24px; }
.summary-value {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
}
.summary-label {
    font-size: 13px;
    color: #6b7280;
}
.invoice-table thead {
    background-color: #f9fafb;
}
.invoice-table th, .invoice-table td {
    padding: 12px 16px;
    font-size: 14px;
    vertical-align: middle;
}
.btn-action { font-size: 14px; }
</style>
@endsection
