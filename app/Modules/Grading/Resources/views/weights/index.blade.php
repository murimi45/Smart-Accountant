@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Assessment weights</h4>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="{{ route('grading.weights.store') }}" class="row g-3">
            @csrf
            <div class="col-md-2"><label class="form-label">Scheme</label><select name="grading_scheme_id" class="form-select" required>@foreach($schemes as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Class</label><select name="class_id" class="form-select" required>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required>@foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Term</label><select name="term_id" class="form-select" required>@foreach($terms as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Type</label><select name="assessment_type_id" class="form-select" required>@foreach($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
            <div class="col-md-1"><label class="form-label">%</label><input type="number" step="0.01" name="weight_percent" class="form-control" required></div>
            <div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary w-100">Save</button></div>
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Class</th><th>Subject</th><th>Term</th><th>Type</th><th>%</th><th></th></tr></thead>
            <tbody>
            @foreach($weights as $weight)
                <tr>
                    <td>{{ $weight->schoolClass?->name }}</td>
                    <td>{{ $weight->subject?->name }}</td>
                    <td>{{ $weight->term?->name }}</td>
                    <td>{{ $weight->assessmentType?->name }}</td>
                    <td>{{ $weight->weight_percent }}</td>
                    <td>
                        <form method="POST" action="{{ route('grading.weights.destroy', $weight) }}">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $weights->links() }}</div>
</div>
@endsection
