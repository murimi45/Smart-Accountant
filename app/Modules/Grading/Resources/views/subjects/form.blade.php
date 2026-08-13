@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <h4 class="mb-3">{{ $subject ? 'Edit subject' : 'Add subject' }}</h4>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ $subject ? route('grading.subjects.update', $subject) : route('grading.subjects.store') }}">
            @csrf
            @if($subject) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input name="name" class="form-control" value="{{ old('name', $subject->name ?? '') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Code</label>
                    <input name="code" class="form-control" value="{{ old('code', $subject->code ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parent learning area</label>
                    <select name="parent_subject_id" class="form-select">
                        <option value="">—</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" @selected(old('parent_subject_id', $subject->parent_subject_id ?? '') == $parent->id)>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-check-label">
                        <input type="checkbox" name="is_learning_area" value="1" class="form-check-input" @checked(old('is_learning_area', $subject->is_learning_area ?? false))>
                        Learning area (CBC)
                    </label>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('grading.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
