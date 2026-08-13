@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Report cards</h4>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    @role('admin')
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="{{ route('grading.report-cards.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Enrollment</label>
                <select name="student_enrollment_id" class="form-select" required>
                    @foreach($enrollments as $enrollment)
                        <option value="{{ $enrollment->id }}">{{ $enrollment->student?->full_name }} ({{ $enrollment->id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Term</label>
                <select name="term_id" class="form-select" required>
                    @foreach($terms as $term)<option value="{{ $term->id }}">{{ $term->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Create draft</button></div>
        </form>
    </div></div>
    @endrole

    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Student</th><th>Term</th><th>Status</th><th>Published</th><th></th></tr></thead>
            <tbody>
            @forelse($cards as $card)
                <tr>
                    <td>{{ $card->enrollment?->student?->full_name }}</td>
                    <td>{{ $card->term?->name }}</td>
                    <td>{{ $card->status }}</td>
                    <td>{{ $card->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="text-end">
                        @role('admin')
                            @if(!$card->isPublished())
                                <form method="POST" action="{{ route('grading.report-cards.publish', $card) }}" class="d-inline">@csrf
                                    <button class="btn btn-sm btn-success">Publish</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('grading.report-cards.unpublish', $card) }}" class="d-inline">@csrf
                                    <button class="btn btn-sm btn-warning">Unpublish</button>
                                </form>
                                <a href="{{ route('grading.report-cards.download', $card) }}" class="btn btn-sm btn-primary">PDF</a>
                            @endif
                        @endrole
                        @role('teacher')
                            @if($card->isPublished())
                                <a href="{{ route('grading.report-cards.download', $card) }}" class="btn btn-sm btn-primary">PDF</a>
                            @endif
                        @endrole
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No report cards.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $cards->links() }}</div>
</div>
@endsection
