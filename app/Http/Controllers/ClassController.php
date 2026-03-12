<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    /**
     * Display list of classes
     */
    public function listClass()
    {
        $data['getRecord'] = Classes::getRecord();
        return view('class.list', $data);
    }

    /**
     * Store multiple classes with Next Class logic
     */
    public function insert(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        // ✅ Validate array of classes
        $request->validate([
            'classes' => 'required|array|min:1',
            'classes.*.name' => 'required|string|max:255',
            'classes.*.next_class_id' => 'nullable|exists:classes,id',
        ]);

        foreach ($request->classes as $data) {

            // Skip empty names
            if (empty($data['name'])) continue;

            // Extract values
            $name = $data['name'];
            $nextClassId = $data['next_class_id'] ?? null;

            $exists = Classes::where('name', $name)->exists();

            if ($exists) {
            return redirect()->route('classlist')
            ->withErrors(['classes.*.name' => "$name already exists."]);
            }

            // Prevent self-reference
            if ($nextClassId && $name == Classes::find($nextClassId)?->name) {
                return redirect()->route('classlist')->withErrors(['classes.*.next_class_id' => 'A class cannot be its own next class.']);
            }

            // Prevent selecting a next class already assigned elsewhere
            if ($nextClassId && Classes::where('next_class_id', $nextClassId)->exists()) {
                return redirect()->route('classlist')->withErrors(['classes.*.next_class_id' => 'That class is already assigned as next class to another.']);
            }

            // Prevent circular reference (A → B and B → A)
            $nextClass = Classes::find($nextClassId);
            if ($nextClass && $nextClass->next_class_id && $nextClass->next_class_id == $name) {
                return redirect()->route('classlist')->withErrors(['classes.*.next_class_id' => 'Circular reference detected.']);
            }

            // ✅ Save each class
            $save = new Classes();
            $save->name = $name;
            $save->next_class_id = $nextClassId;
            $save->school_id = $schoolId;
            // $save->created_by = Auth::id();
            $save->save();
        }

        return redirect()->route('classlist')->with('success', 'Classes successfully created.');
    }

    /**
     * Update Class
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'next_class_id' => 'nullable|exists:classes,id',
        ]);

        // Prevent self reference
        if ($id == $request->next_class_id) {
            return redirect()->route('classlist')->withErrors(['next_class_id' => 'A class cannot be its own next class.']);
        }

        // Prevent duplicate next class assignment
        if ($request->next_class_id && Classes::where('next_class_id', $request->next_class_id)
            ->where('id', '!=', $id)
            ->exists()) {
            return redirect()->route('classlist')->withErrors(['next_class_id' => 'That class is already assigned as next class to another class.']);
        }

        // Prevent circular reference (A→B and B→A)
        $nextClass = Classes::find($request->next_class_id);
        if ($nextClass && $nextClass->next_class_id == $id) {
            return redirect()->route('classlist')->withErrors(['next_class_id' => 'Circular reference detected between the two classes.']);
        }

        // Save update
        $save = Classes::findOrFail($id);
        $save->name = $request->name;
        $save->next_class_id = $request->next_class_id;
        $save->save();

        return redirect()->route('classlist')->with('success', 'Class updated successfully.');
    }

    /**
     * Delete Class
     */
    public function delete($id)
    {
        $classes = Classes::findOrFail($id);
        $classes->delete();

        return redirect()->route('classlist')->with('success', 'Deleted successfully.');
    }
}