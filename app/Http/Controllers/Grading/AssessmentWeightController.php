<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\AssessmentType;
use App\Models\AssessmentWeight;
use App\Models\Classes;
use App\Models\GradingScheme;
use App\Models\Subject;
use App\Models\Term;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentWeightController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $weights = AssessmentWeight::query()
            ->with(['schoolClass', 'subject', 'term', 'assessmentType', 'gradingScheme'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('grading::weights.index', [
            'weights' => $weights,
            'classes' => Classes::query()->orderBy('order')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'terms' => Term::query()->orderBy('term_number')->get(),
            'types' => AssessmentType::query()->orderBy('name')->get(),
            'schemes' => GradingScheme::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'grading_scheme_id' => ['required', 'exists:grading_schemes,id'],
            'class_id' => ['required', TenantRules::classes()],
            'subject_id' => ['required', TenantRules::exists('subjects')],
            'term_id' => ['required', TenantRules::terms()],
            'assessment_type_id' => ['required', TenantRules::exists('assessment_types')],
            'weight_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        AssessmentWeight::query()->updateOrCreate(
            [
                'class_id' => $data['class_id'],
                'subject_id' => $data['subject_id'],
                'term_id' => $data['term_id'],
                'assessment_type_id' => $data['assessment_type_id'],
            ],
            [
                'grading_scheme_id' => $data['grading_scheme_id'],
                'weight_percent' => $data['weight_percent'],
            ]
        );

        return back()->with('success', 'Weight saved.');
    }

    public function destroy(AssessmentWeight $weight): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $weight->delete();

        return back()->with('success', 'Weight deleted.');
    }
}
