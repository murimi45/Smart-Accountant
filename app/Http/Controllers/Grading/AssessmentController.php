<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Classes;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Term;
use App\Modules\Grading\Services\RosterService;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Assessment::class);

        $assessments = Assessment::query()
            ->with(['schoolClass', 'subject', 'term', 'assessmentType', 'stream'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('grading::assessments.index', compact('assessments'));
    }

    public function create(): View
    {
        $this->authorize('create', Assessment::class);

        return view('grading::assessments.form', [
            'assessment' => null,
            'classes' => Classes::query()->orderBy('order')->get(),
            'streams' => Stream::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'terms' => Term::query()->orderBy('term_number')->get(),
            'types' => AssessmentType::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, RosterService $roster): RedirectResponse
    {
        $this->authorize('create', Assessment::class);

        $data = $request->validate([
            'class_id' => ['required', TenantRules::classes()],
            'stream_id' => ['nullable', TenantRules::streamInSchool()],
            'subject_id' => ['required', TenantRules::exists('subjects')],
            'term_id' => ['required', TenantRules::terms()],
            'assessment_type_id' => ['required', TenantRules::exists('assessment_types')],
            'title' => ['required', 'string', 'max:255'],
            'assessed_on' => ['nullable', 'date'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in([Assessment::STATUS_DRAFT, Assessment::STATUS_OPEN])],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $user = auth()->user();
        if (! $roster->teacherCanAccess(
            $user,
            (int) $data['class_id'],
            (int) $data['subject_id'],
            isset($data['stream_id']) ? (int) $data['stream_id'] : null,
            (int) $data['term_id']
        )) {
            abort(403, 'You are not assigned to this class/subject.');
        }

        Assessment::query()->create($data);

        return redirect()->route('grading.assessments.index')->with('success', 'Assessment created.');
    }

    public function edit(Assessment $assessment): View
    {
        $this->authorize('update', $assessment);

        return view('grading::assessments.form', [
            'assessment' => $assessment,
            'classes' => Classes::query()->orderBy('order')->get(),
            'streams' => Stream::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'terms' => Term::query()->orderBy('term_number')->get(),
            'types' => AssessmentType::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorize('update', $assessment);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assessed_on' => ['nullable', 'date'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in([
                Assessment::STATUS_DRAFT,
                Assessment::STATUS_OPEN,
                Assessment::STATUS_CLOSED,
            ])],
        ]);

        $assessment->update($data);

        return redirect()->route('grading.assessments.index')->with('success', 'Assessment updated.');
    }

    public function destroy(Assessment $assessment): RedirectResponse
    {
        $this->authorize('delete', $assessment);
        $assessment->delete();

        return redirect()->route('grading.assessments.index')->with('success', 'Assessment deleted.');
    }
}
