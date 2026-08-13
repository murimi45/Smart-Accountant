@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Teacher assignments</h4>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="{{ route('grading.assignments.store') }}" class="row g-3">
            @csrf
            <div class="col-md-2"><label class="form-label">Teacher</label><select name="user_id" class="form-select" required>@foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->admin_name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Class</label><select name="class_id" class="form-select" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Stream</label><select name="stream_id" class="form-select"><option value="">Any</option>@foreach($streams as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required>@foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Year</label><select name="academic_year_id" class="form-select" required>@foreach($years as $y)<option value="{{ $y->id }}">{{ $y->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Term</label><select name="term_id" class="form-select"><option value="">Year</option>@foreach($terms as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
            <div class="col-12"><button class="btn btn-primary">Assign</button></div>
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Teacher</th><th>Class</th><th>Stream</th><th>Subject</th><th>Year</th><th></th></tr></thead>
            <tbody>
            @foreach($assignments as $a)
                <tr>
                    <td>{{ $a->user?->admin_name }}</td>
                    <td>{{ $a->schoolClass?->name }}</td>
                    <td>{{ $a->stream?->name ?? '—' }}</td>
                    <td>{{ $a->subject?->name }}</td>
                    <td>{{ $a->academicYear?->name }}</td>
                    <td>
                        <form method="POST" action="{{ route('grading.assignments.destroy', $a) }}">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $assignments->links() }}</div>
</div>
@endsection
