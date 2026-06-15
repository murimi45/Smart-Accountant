<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', AcademicYear::class);

        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('academic_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AcademicYear::class);

        $schoolId = Auth::user()->school_id;

        $request->validate([
            'name'       => 'required|string|max:255|unique:academic_years,name,NULL,id,school_id,' . $schoolId,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
            'school_id'  => 'prohibited',
        ]);

        if ($request->boolean('is_current')) {
            AcademicYear::forSchool($schoolId)->update(['is_current' => false]);
        }

        AcademicYear::create([
            'name'        => $request->name,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_current'  => $request->boolean('is_current'),
        ]);

        return redirect()->route('academic-years.index')
            ->with('success', 'Academic Year created successfully.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $this->authorize('update', $academicYear);

        $schoolId = Auth::user()->school_id;
        AcademicYear::forSchool($schoolId)->findOrFail($academicYear->id);

        $request->validate([
            'name'       => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id . ',id,school_id,' . $schoolId,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
            'school_id'  => 'prohibited',
        ]);

        if ($request->boolean('is_current')) {
            AcademicYear::forSchool($schoolId)
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

    public function destroy(AcademicYear $academicYear)
    {
        $this->authorize('delete', $academicYear);

        AcademicYear::forSchool()->findOrFail($academicYear->id);

        if ($academicYear->is_current) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Cannot delete the current academic year.');
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')
            ->with('success', 'Academic Year deleted successfully.');
    }
}
