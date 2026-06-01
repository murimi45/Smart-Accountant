<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Term;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    private function enrollmentForEdit(Student $student, ?Term $activeTerm): ?StudentEnrollment
    {
        if ($activeTerm) {
            $enrollment = StudentEnrollment::where('student_id', $student->id)
                ->where('term_id', $activeTerm->id)
                ->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED])
                ->latest()
                ->first();

            if ($enrollment) {
                return $enrollment;
            }
        }

        return StudentEnrollment::where('student_id', $student->id)
            ->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED])
            ->latest()
            ->first();
    }

    public function listStudents(Request $request)
    {
        $data['classes'] = Classes::all();
        $data['terms']   = Term::all();
        $data['getRecord'] = Student::getRecord($request)->paginate(10);

        return view('student.list', $data);
    }

    public function addStudents()
    {
        $schoolId = Auth::user()->school_id;
        $activeTerm = Term::current1($schoolId);

        $data['classes'] = Classes::orderBy('order')->get();
        $data['terms'] = Term::with('academicYear')
            ->where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();
        $data['activeTerm'] = $activeTerm;

        return view('student.add', $data);
    }

    public function insertStudents(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'admission' => 'required|string|unique:students,admission',
            'guardian_name' => 'nullable|string|max:255',
            'gender'    => 'required|in:male,female',
            'class_id'  => 'required|exists:classes,id',
            'term_id'   => 'required|exists:terms,id',
        ]);

        $student = Student::create([
            'school_id' => $schoolId,
            'full_name' => $validated['name'],
            'phone'     => $validated['phone'] ?? null,
            'admission' => $validated['admission'],
            'gender'    => $validated['gender'],
            'guardian_name' => $validated['guardian_name'] ?? null,
        ]);

        StudentEnrollment::create([
            'school_id'  => $schoolId,
            'student_id' => $student->id,
            'class_id'   => $validated['class_id'],
            'term_id'    => $validated['term_id'],
            'status'     => 'active',
        ]);

        return redirect()->route('listStudents')->with('success', 'Student added successfully.');
    }

    public function editStudents($id)
    {
        $schoolId = Auth::user()->school_id;
        $student = Student::findOrFail($id);
        $activeTerm = Term::current1($schoolId);
        $enrollment = $this->enrollmentForEdit($student, $activeTerm);

        $terms = Term::with('academicYear')
            ->where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();
        $classes = Classes::orderBy('order')->get();

        return view('student.edit', compact('student', 'terms', 'classes', 'activeTerm', 'enrollment'));
    }

    public function updateStudents(Request $request, $id)
    {
        $student  = Student::findOrFail($id);
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'admission' => 'required|string|unique:students,admission,' . $id,
            'gender'    => 'required|in:male,female',
            'class_id'  => 'required|exists:classes,id',
            'term_id'   => 'required|exists:terms,id',
            'guardian_name' => 'nullable|string|max:255',
        ]);

        $student->update([
            'full_name' => $validated['name'],
            'phone'     => $validated['phone'] ?? null,
            'admission' => $validated['admission'],
            'gender'    => $validated['gender'],
            'guardian_name' => $validated['guardian_name'] ?? null,
        ]);

        StudentEnrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'term_id'    => $validated['term_id'],
            ],
            [
                'school_id' => $schoolId,
                'class_id'  => $validated['class_id'],
                'status'    => 'active',
            ]
        );

        return redirect()->route('listStudents')->with('success', 'Student updated successfully.');
    }

    public function deleteStudent($id)
    {
        Student::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Deleted successfully.');
    }
}
