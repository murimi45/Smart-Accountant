<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\AcademicYear;
use App\Support\TenantRules;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function listTerm()
    {
        $schoolId = auth()->user()->school_id;

        $terms = Term::with('academicYear')
            ->orderByDesc('start_date')
            ->get();

        $termId      = Term::currentId();
        $currentTerm = Term::current();
        $nextTerm    = Term::nextAfter($schoolId, $currentTerm);

        return view('term.list', compact('terms', 'termId', 'currentTerm', 'nextTerm'));
    }

    public function addTerm()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('term.add', compact('academicYears'));
    }

    public function insertTerm(Request $request)
    {
        $this->authorize('create', Term::class);

        $schoolId = auth()->user()->school_id;

        $request->validate([
            'name'             => 'required|string|max:50',
            'academic_year_id' => ['required', TenantRules::academicYears()],
            'term_number'      => 'required|integer|min:1|max:4',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'school_id'        => TenantRules::prohibitedSchoolId(),
        ]);

        $exists = Term::forSchool($schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('term_number', $request->term_number)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A term with this number already exists under the selected academic year.');
        }

        $hasExistingTerm = Term::forSchool($schoolId)->exists();

        Term::create([
            'name'             => $request->name,
            'academic_year_id' => $request->academic_year_id,
            'term_number'      => $request->term_number,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'active'           => $hasExistingTerm ? false : true,
        ]);

        return redirect()->route('termlist')
            ->with('success', $hasExistingTerm
                ? 'Term created successfully.'
                : 'First term created and set as active.');
    }

    public function updateTerm($id)
    {
        $term = Term::forSchool()->findOrFail($id);
        $this->authorize('update', $term);
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('term.edit', compact('term', 'academicYears'));
    }

    public function editTerm($id, Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $term = Term::forSchool($schoolId)->findOrFail($id);
        $this->authorize('update', $term);

        $request->validate([
            'name'             => 'required|string|max:50',
            'academic_year_id' => ['required', TenantRules::academicYears()],
            'term_number'      => 'required|integer|min:1|max:4',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'active'           => 'nullable|boolean',
            'school_id'        => TenantRules::prohibitedSchoolId(),
        ]);

        $exists = Term::forSchool($schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('term_number', $request->term_number)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A term with this number already exists under the selected academic year.');
        }

        $isActive = $request->boolean('active');

        if ($isActive) {
            Term::forSchool($schoolId)
                ->where('id', '!=', $id)
                ->update(['active' => false]);
        }

        $term->update([
            'name'             => $request->name,
            'academic_year_id' => $request->academic_year_id,
            'term_number'      => $request->term_number,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'active'           => $isActive,
        ]);

        return redirect()->route('termlist')->with('success', 'Term updated successfully.');
    }

    public function delete($id)
    {
        $term = Term::forSchool()->findOrFail($id);
        $this->authorize('delete', $term);
        $term->delete();

        return redirect()->back()->with('success', 'Term deleted successfully.');
    }
}
