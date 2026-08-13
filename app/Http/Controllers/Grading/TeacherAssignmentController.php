<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\TeacherSubjectAssignment;
use App\Models\Term;
use App\Models\User;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherAssignmentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TeacherSubjectAssignment::class);

        $assignments = TeacherSubjectAssignment::query()
            ->with(['user', 'schoolClass', 'stream', 'subject', 'academicYear', 'term'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('grading::assignments.index', [
            'assignments' => $assignments,
            'teachers' => User::query()
                ->where('school_id', auth()->user()->school_id)
                ->where('role', User::ROLE_TEACHER)
                ->orderBy('admin_name')
                ->get(),
            'classes' => Classes::query()->orderBy('order')->get(),
            'streams' => Stream::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'years' => AcademicYear::query()->orderByDesc('is_current')->get(),
            'terms' => Term::query()->orderBy('term_number')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', TeacherSubjectAssignment::class);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'class_id' => ['required', TenantRules::classes()],
            'stream_id' => ['nullable', TenantRules::streamInSchool()],
            'subject_id' => ['required', TenantRules::exists('subjects')],
            'academic_year_id' => ['required', TenantRules::academicYears()],
            'term_id' => ['nullable', TenantRules::terms()],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $teacher = User::query()
            ->where('school_id', auth()->user()->school_id)
            ->where('role', User::ROLE_TEACHER)
            ->findOrFail($data['user_id']);

        TeacherSubjectAssignment::query()->create([
            'user_id' => $teacher->id,
            'class_id' => $data['class_id'],
            'stream_id' => $data['stream_id'] ?? null,
            'subject_id' => $data['subject_id'],
            'academic_year_id' => $data['academic_year_id'],
            'term_id' => $data['term_id'] ?? null,
        ]);

        return back()->with('success', 'Teacher assignment created.');
    }

    public function destroy(TeacherSubjectAssignment $assignment): RedirectResponse
    {
        $this->authorize('delete', $assignment);
        $assignment->delete();

        return back()->with('success', 'Assignment removed.');
    }
}
