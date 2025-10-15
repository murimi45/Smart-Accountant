<?php

namespace App\Http\Controllers;
use App\Models\Term;
use Auth;

use Illuminate\Http\Request;

class TermController extends Controller
{
    
      public function listTerm()
    {
        $data['getRecord'] = Term::where('school_id', auth()->user()->school_id)
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();

        $termId = Term::currentId();
        $currentTerm = Term::current();
        $terms = Term::get();
        $data['termId'] = $termId;
        $data['currentTerm'] = $currentTerm;
        $data['terms'] = $terms;

        return view('term.list', $data);
    }

    public function addTerm(){
          
        return view('term.add');
    }

    public function insertTerm(Request $request)
{
    $schoolId = auth()->user()->school_id;

    $request->validate([
        'name'       => 'required|string|max:50',
        'year'       => 'required|digits:4|integer',
        'start_date' => 'required|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ]);

    // Prevent duplicate terms in same year
    $exists = Term::where('school_id', $schoolId)
        ->where('name', $request->name)
        ->where('year', $request->year)
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'A term with this name and year already exists.');
    }

    // ✅ Check if this is the school's first term
    $hasExistingTerm = Term::where('school_id', $schoolId)->exists();

    // Create new term
    $term = new Term();
    $term->school_id  = $schoolId;
    $term->name       = $request->name;
    $term->year       = $request->year;
    $term->start_date = $request->start_date;
    $term->end_date   = $request->end_date;
    $term->active     = $hasExistingTerm ? false : true; // First term auto-active
    $term->save();

    return redirect()->route('termlist')
        ->with('success', $hasExistingTerm
            ? 'Term created successfully.'
            : 'First term created and set as active.');
}



    public function editTerm($id,Request $request){

        $schoolId = auth()->user()->school_id;

        $term = Term::where('id', $id)->where('school_id', $schoolId)->firstOrFail();

        // ✅ Validation
        $request->validate([
            'name'       => 'required|string|max:50',
            'year'       => 'required|digits:4|integer',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'active'     => 'nullable|boolean',
        ]);

      
       

        $term->name       = $request->name;
        $term->year       = $request->year;
        $term->start_date = $request->start_date;
        $term->end_date   = $request->end_date;
        $term->active    = $request->active ?? false;
        $term->save();

        return redirect()->route('termlist')->with('success', 'Term Updated Successfully');
    }

    public function updateTerm($id,Request $request){
           $schoolId = auth()->user()->school_id;

           $data['getRecord'] = Term::where('id', $id)->where('school_id', $schoolId)->firstOrFail();

           return view('term.edit', $data);

    }

     public function delete($id){
        $classes=Term::findOrFail($id);
        $classes->delete();

        return redirect()->back()->with('success','Deleted Successfully');
    }

}
