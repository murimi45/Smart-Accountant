@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="d-flex justify-content-between mb-3">
        <h4>Subjects / learning areas</h4>
        <a href="{{ route('grading.subjects.create') }}" class="btn btn-success">Add</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Code</th><th>Learning area</th><th>Parent</th><th></th></tr></thead>
            <tbody>
            @forelse($subjects as $subject)
                <tr>
                    <td>{{ $subject->name }}</td>
                    <td>{{ $subject->code }}</td>
                    <td>{{ $subject->is_learning_area ? 'Yes' : 'No' }}</td>
                    <td>{{ $subject->parent?->name }}</td>
                    <td class="text-end">
                        <a href="{{ route('grading.subjects.edit', $subject) }}" class="btn btn-sm btn-light">Edit</a>
                        <form action="{{ route('grading.subjects.destroy', $subject) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No subjects yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $subjects->links() }}</div>
</div>
@endsection
