@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Grading</h4>
        <p class="text-muted mb-0">CBC and International assessments, mark entry, and report cards.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card"><div class="card-body"><div class="text-muted">Open assessments</div><div class="fs-4">{{ $openAssessments }}</div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body"><div class="text-muted">Class subjects</div><div class="fs-4">{{ $classSubjects }}</div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body"><div class="text-muted">Draft report cards</div><div class="fs-4">{{ $draftCards }}</div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body"><div class="text-muted">Published cards</div><div class="fs-4">{{ $publishedCards }}</div></div></div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2 flex-wrap">
        <a href="{{ route('grading.mark-entry.index') }}" class="btn btn-primary">Mark Entry</a>
        <a href="{{ route('grading.assessments.index') }}" class="btn btn-outline-secondary">Assessments</a>
        <a href="{{ route('grading.report-cards.index') }}" class="btn btn-outline-secondary">Report Cards</a>
        @role('admin')
            <a href="{{ route('grading.settings.index') }}" class="btn btn-outline-secondary">Setup</a>
        @endrole
    </div>
</div>
@endsection
