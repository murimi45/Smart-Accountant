<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Subject::class);
        $subjects = Subject::query()->with('parent')->orderBy('name')->paginate(20);

        return view('grading::subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $this->authorize('create', Subject::class);
        $parents = Subject::query()->where('is_learning_area', true)->orderBy('name')->get();

        return view('grading::subjects.form', ['subject' => null, 'parents' => $parents]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Subject::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', TenantRules::unique('subjects', 'code')],
            'is_learning_area' => ['sometimes', 'boolean'],
            'parent_subject_id' => ['nullable', TenantRules::exists('subjects')],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        Subject::query()->create([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'is_learning_area' => $request->boolean('is_learning_area'),
            'parent_subject_id' => $data['parent_subject_id'] ?? null,
        ]);

        return redirect()->route('grading.subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject): View
    {
        $this->authorize('update', $subject);
        $parents = Subject::query()
            ->where('is_learning_area', true)
            ->where('id', '!=', $subject->id)
            ->orderBy('name')
            ->get();

        return view('grading::subjects.form', compact('subject', 'parents'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', TenantRules::unique('subjects', 'code', $subject->id)],
            'is_learning_area' => ['sometimes', 'boolean'],
            'parent_subject_id' => ['nullable', TenantRules::exists('subjects')],
        ]);

        $subject->update([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'is_learning_area' => $request->boolean('is_learning_area'),
            'parent_subject_id' => $data['parent_subject_id'] ?? null,
        ]);

        return redirect()->route('grading.subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $this->authorize('delete', $subject);
        $subject->delete();

        return redirect()->route('grading.subjects.index')->with('success', 'Subject deleted.');
    }
}
