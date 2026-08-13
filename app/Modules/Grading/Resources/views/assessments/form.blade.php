@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">{{ $assessment ? 'Edit assessment' : 'Create assessment' }}</h4>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ $assessment ? route('grading.assessments.update', $assessment) : route('grading.assessments.store') }}" class="row g-3">
            @csrf
            @if($assessment) @method('PUT') @endif
            @unless($assessment)
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stream</label>
                <select name="stream_id" class="form-select"><option value="">All</option>@foreach($streams as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required>@foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Term</label>
                <select name="term_id" class="form-select" required>@foreach($terms as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="assessment_type_id" class="form-select" required>@foreach($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select>
            </div>
            @endunless
            <div class="col-md-4">
                <label class="form-label">Title</label>
                <input name="title" class="form-control" value="{{ old('title', $assessment->title ?? '') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date</label>
                <input type="date" name="assessed_on" class="form-control" value="{{ old('assessed_on', optional($assessment->assessed_on ?? null)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Max score</label>
                <input type="number" step="0.01" name="max_score" class="form-control" value="{{ old('max_score', $assessment->max_score ?? '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    @foreach(['draft','open','closed'] as $status)
                        @if(!$assessment && $status === 'closed') @continue @endif
                        <option value="{{ $status }}" @selected(old('status', $assessment->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('grading.assessments.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
