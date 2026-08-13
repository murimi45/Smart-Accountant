@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="d-flex justify-content-between mb-3">
        <h4>Class subjects</h4>
        <a href="{{ route('grading.class-subjects.create') }}" class="btn btn-success">Link subject</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Class</th><th>Subject</th><th>Year</th><th>Term</th><th></th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->schoolClass?->name }}</td>
                    <td>{{ $item->subject?->name }}</td>
                    <td>{{ $item->academicYear?->name }}</td>
                    <td>{{ $item->term?->name ?? 'Whole year' }}</td>
                    <td>
                        <form method="POST" action="{{ route('grading.class-subjects.destroy', $item) }}">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Remove?')">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">None yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $items->links() }}</div>
</div>
@endsection
