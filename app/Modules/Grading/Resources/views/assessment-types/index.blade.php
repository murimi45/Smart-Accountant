@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">Assessment types</h4>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="{{ route('grading.assessment-types.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Slug</label><input name="slug" class="form-control" placeholder="auto"></div>
            <div class="col-md-3"><label class="form-check-label"><input type="checkbox" name="is_competency" value="1" class="form-check-input"> Competency</label></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Slug</th><th>Competency</th><th></th></tr></thead>
            <tbody>
            @foreach($types as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->slug }}</td>
                    <td>{{ $type->is_competency ? 'Yes' : 'No' }}</td>
                    <td>
                        <form method="POST" action="{{ route('grading.assessment-types.destroy', $type) }}">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $types->links() }}</div>
</div>
@endsection
