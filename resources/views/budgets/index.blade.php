@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">Expense Budgets</h4>
                <p class="text-muted mb-0">Set spending limits per category for each term</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('reports.budget-variance', ['term_id' => $termId]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-chart-bar me-1"></i>Variance Report
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('budgets.index') }}" class="row g-3 align-items-end">
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
            </form>
        </div>
    </div>

    @if($selectedTerm)
        <form method="POST" action="{{ route('budgets.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="term_id" value="{{ $termId }}">

            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Budget for {{ $selectedTerm->name }} {{ $selectedTerm->year }}</h5>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save me-1"></i>Save Budgets
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th style="width: 180px;">Budget (KSh)</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    @php $budget = $budgets->get($category->id); @endphp
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <input type="number"
                                                   name="amounts[{{ $category->id }}]"
                                                   class="form-control form-control-sm"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('amounts.'.$category->id, $budget?->amount) }}"
                                                   placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="notes[{{ $category->id }}]"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('notes.'.$category->id, $budget?->notes) }}"
                                                   placeholder="Optional note">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No expense categories yet.
                                            <a href="{{ route('expense_categories.index') }}">Add categories</a> first.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-warning">Select a term to set budgets.</div>
    @endif
</div>
@endsection
