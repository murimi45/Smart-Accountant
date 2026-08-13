@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Link subject to class</h4>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('grading.class-subjects.store') }}" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" required>
                    @foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required>
                    @foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Academic year</label>
                <select name="academic_year_id" class="form-select" required>
                    @foreach($years as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Term (optional)</label>
                <select name="term_id" class="form-select">
                    <option value="">Whole year</option>
                    @foreach($terms as $term)<option value="{{ $term->id }}">{{ $term->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('grading.class-subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
