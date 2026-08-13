@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="d-flex justify-content-between mb-3">
        <h4>Assessments</h4>
        <a href="{{ route('grading.assessments.create') }}" class="btn btn-success">Create</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Title</th><th>Class</th><th>Subject</th><th>Term</th><th>Type</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($assessments as $assessment)
                <tr>
                    <td>{{ $assessment->title }}</td>
                    <td>{{ $assessment->schoolClass?->name }}</td>
                    <td>{{ $assessment->subject?->name }}</td>
                    <td>{{ $assessment->term?->name }}</td>
                    <td>{{ $assessment->assessmentType?->name }}</td>
                    <td>{{ $assessment->status }}</td>
                    <td class="text-end">
                        @if($assessment->isOpen())
                            <a href="{{ route('grading.mark-entry.show', $assessment) }}" class="btn btn-sm btn-primary">Enter</a>
                        @endif
                        <a href="{{ route('grading.assessments.edit', $assessment) }}" class="btn btn-sm btn-light">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-4">No assessments.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $assessments->links() }}</div>
</div>
@endsection
