<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Term;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassSubjectController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ClassSubject::class);
        $items = ClassSubject::query()
            ->with(['schoolClass', 'subject', 'academicYear', 'term'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('grading::class-subjects.index', compact('items'));
    }

    public function create(): View
    {
        $this->authorize('create', ClassSubject::class);

        return view('grading::class-subjects.form', [
            'item' => null,
            'classes' => Classes::query()->orderBy('order')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'years' => AcademicYear::query()->orderByDesc('is_current')->get(),
            'terms' => Term::query()->orderBy('term_number')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ClassSubject::class);

        $data = $request->validate([
            'class_id' => ['required', TenantRules::classes()],
            'subject_id' => ['required', TenantRules::exists('subjects')],
            'academic_year_id' => ['required', TenantRules::academicYears()],
            'term_id' => ['nullable', TenantRules::terms()],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        ClassSubject::query()->create($data);

        return redirect()->route('grading.class-subjects.index')->with('success', 'Class subject linked.');
    }

    public function destroy(ClassSubject $classSubject): RedirectResponse
    {
        $this->authorize('delete', $classSubject);
        $classSubject->delete();

        return redirect()->route('grading.class-subjects.index')->with('success', 'Class subject removed.');
    }
}
