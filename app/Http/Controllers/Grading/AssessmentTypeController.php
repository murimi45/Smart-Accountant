<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\AssessmentType;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssessmentTypeController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $types = AssessmentType::query()->orderBy('name')->paginate(20);

        return view('grading::assessment-types.index', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:50', TenantRules::unique('assessment_types', 'slug')],
            'is_competency' => ['sometimes', 'boolean'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        AssessmentType::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'is_competency' => $request->boolean('is_competency'),
        ]);

        return back()->with('success', 'Assessment type created.');
    }

    public function destroy(AssessmentType $assessmentType): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $assessmentType->delete();

        return back()->with('success', 'Assessment type deleted.');
    }
}
