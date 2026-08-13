@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Grading scheme by academic year</h4>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('grading.settings.store') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Academic year</label>
                    <select name="academic_year_id" class="form-select" required>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}{{ $year->is_current ? ' (current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scheme</label>
                    <select name="grading_scheme_id" class="form-select" required>
                        @foreach($catalog as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Year</th><th>Scheme</th></tr></thead>
                <tbody>
                    @forelse($years as $year)
                        <tr>
                            <td>{{ $year->name }}</td>
                            <td>{{ $settings[$year->id]->gradingScheme->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-4">No academic years yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
