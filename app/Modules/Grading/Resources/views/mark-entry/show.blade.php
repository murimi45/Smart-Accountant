@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="mb-3">
        <a href="{{ route('grading.mark-entry.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <h4 class="mb-1">{{ $assessment->title }}</h4>
    <p class="text-muted">{{ $assessment->schoolClass?->name }} · {{ $assessment->subject?->name }} · {{ $assessment->term?->name }}</p>

    @livewire('grading.mark-entry-grid', ['assessment' => $assessment])
</div>
@endsection
