@extends('layouts.app')
@section('main')

@php
    $reportTitles = [
        'trial-balance' => 'Trial Balance',
        'profit-loss' => 'Profit & Loss',
        'balance-sheet' => 'Balance Sheet',
    ];
    $title = $reportTitles[$reportType] ?? 'Financial Report';
    $exportQuery = http_build_query(array_filter([
        'view' => $viewType,
        'report' => $reportType,
        'term_id' => $viewType === 'term' ? $termId : null,
        'academic_year_id' => $viewType === 'year' ? $academicYearId : null,
    ]));
@endphp

<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">{{ $title }}</h4>
        <p class="text-muted mb-0">From general ledger — cash-basis postings from cashbook activity</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4">
        @foreach($reportTitles as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $reportType === $key ? 'active' : '' }}"
                   href="{{ route('reports.financial', array_filter(['report' => $key, 'view' => $viewType, 'term_id' => $viewType === 'term' ? $termId : null, 'academic_year_id' => $viewType === 'year' ? $academicYearId : null])) }}">
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.financial') }}">
                <input type="hidden" name="report" value="{{ $reportType }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">View</label>
                        <select name="view" class="form-select" onchange="this.form.submit()">
                            <option value="term" {{ $viewType === 'term' ? 'selected' : '' }}>Term</option>
                            <option value="year" {{ $viewType === 'year' ? 'selected' : '' }}>Academic Year</option>
                        </select>
                    </div>
                    @if($viewType === 'term')
                        <div class="col-md-4">
                            <label class="form-label">Term</label>
                            <select name="term_id" class="form-select" onchange="this.form.submit()">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ (int) $termId === (int) $term->id ? 'selected' : '' }}>
                                        {{ $term->name }} - {{ $term->year }}
                                        @if($currentTerm && $term->id === $currentTerm->id) (current) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ (int) $academicYearId === (int) $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('reports.financial.pdf') }}?{{ $exportQuery }}" class="btn btn-outline-danger">
                            <i class="fa fa-file-pdf me-1"></i>Export PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-3">
        <strong>Period:</strong> {{ $period['label'] }}
        ({{ $period['start']->format('d M Y') }} – {{ $period['end']->format('d M Y') }})
    </p>

    @if($reportType === 'trial-balance')
        @include('reports.financial.partials.trial-balance')
    @elseif($reportType === 'profit-loss')
        @include('reports.financial.partials.profit-loss')
    @else
        @include('reports.financial.partials.balance-sheet')
    @endif
</div>
@endsection
