<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Term;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    /**
     * Display list of classes
     */
    public function listClass()
    {
        $schoolId = Auth::user()->school_id;
        $activeTerm = Term::current1($schoolId);
        $currentAcademicYear = $activeTerm?->academicYear;

        $data['getRecord'] = Classes::getRecord();
        $data['academicYears'] = AcademicYear::after($schoolId, $currentAcademicYear)->get();
        $data['nextAcademicYear'] = AcademicYear::nextAfter($schoolId, $currentAcademicYear);
        $data['currentAcademicYear'] = $currentAcademicYear;

        return view('class.list', $data);
    }

    /**
     * Store multiple classes
     */
    public function insert(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'classes' => 'required|array|min:1',
            'classes.*.name' => 'required|string|max:255',
            'classes.*.order' => 'required|integer|min:1',
        ]);

        // Check for duplicate orders in the request payload
        $orders = collect($request->classes)->pluck('order');
        if ($orders->count() !== $orders->unique()->count()) {
            return redirect()->back()
                ->withErrors(['classes' => 'Duplicate order values detected.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($request->classes as $classData) {
                $save = new Classes();
                $save->name = $classData['name'];
                $save->order = $classData['order'];
                $save->school_id = $schoolId;
                $save->save();
            }

            DB::commit();

            return redirect()->route('classlist')
                ->with('success', 'Classes successfully created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create classes: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update Class - Now using 'order' instead of next_class_id
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
        ]);

        $class = Classes::findOrFail($id);


        // Check if the new order is already taken by another class (of the same school)
        $orderExists = Classes::where('order', $request->order)
            ->where('id', '!=', $id)
            ->where('school_id', $class->school_id)
            ->exists();

        if ($orderExists) {
            return redirect()->route('classlist')
                ->withErrors(['order' => 'This order is already assigned to another class.']);
        }

        $class->name = $request->name;
        $class->order = $request->order;
        $class->save();

        return redirect()->route('classlist')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Delete Class
     */
    public function delete($id)
    {
        $class = Classes::findOrFail($id);

        // Security check
        if ($class->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $class->delete();

        return redirect()->route('classlist')
            ->with('success', 'Class deleted successfully.');
    }
}