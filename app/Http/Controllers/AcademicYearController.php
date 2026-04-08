<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of Academic Years
     */
    public function index()
    {
        $years = AcademicYear::all(); // Global scope should handle school filtering
        return view('academic_years.index', compact('years'));
    }

    /**
     * Store a newly created Academic Year (from Modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:academic_years,name,NULL,id,school_id,' . Auth::user()->school_id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        // If user wants to set this as current year, unset all others first
        if ($request->boolean('is_current')) {
            AcademicYear::where('school_id', Auth::user()->school_id)
                        ->update(['is_current' => false]);
        }

        AcademicYear::create([
            'school_id'   => Auth::user()->school_id,
            'name'        => $request->name,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_current'  => $request->boolean('is_current'),
        ]);

        return redirect()->route('academic-years.index')
                         ->with('success', 'Academic Year created successfully.');
    }

    /**
     * Update the specified Academic Year (from Modal)
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        // Authorization: Ensure user can only edit their school's year
        if ($academicYear->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'       => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id . ',id,school_id,' . Auth::user()->school_id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        // If setting as current, reset all other years
        if ($request->boolean('is_current')) {
            AcademicYear::where('school_id', Auth::user()->school_id)
                        ->where('id', '!=', $academicYear->id)
                        ->update(['is_current' => false]);
        }

        $academicYear->update([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()->route('academic-years.index')
                         ->with('success', 'Academic Year updated successfully.');
    }

    /**
     * Remove the specified Academic Year
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Authorization check
        if ($academicYear->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Optional: Prevent deleting current academic year
        if ($academicYear->is_current) {
            return redirect()->route('academic-years.index')
                             ->with('error', 'Cannot delete the current academic year.');
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')
                         ->with('success', 'Academic Year deleted successfully.');
    }
}