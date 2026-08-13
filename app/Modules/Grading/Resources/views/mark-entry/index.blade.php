@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Mark entry</h4>
    <p class="text-muted">Open assessments available for entry.</p>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Title</th><th>Class</th><th>Subject</th><th>Term</th><th></th></tr></thead>
            <tbody>
            @forelse($assessments as $assessment)
                <tr>
                    <td>{{ $assessment->title }}</td>
                    <td>{{ $assessment->schoolClass?->name }}</td>
                    <td>{{ $assessment->subject?->name }}</td>
                    <td>{{ $assessment->term?->name }}</td>
                    <td><a href="{{ route('grading.mark-entry.show', $assessment) }}" class="btn btn-sm btn-primary">Open grid</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No open assessments.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
