<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PromotionService;
use App\Models\Term;
use App\Models\Student;
use App\Models\PromotionRun;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Promote students to the next term
     */
    public function promoteToNextTerm(Request $request)
    {
        $request->validate([
            'from_term_id' => 'required|exists:terms,id',
            'to_term_id' => 'required|exists:terms,id',
        ]);

        $schoolId = Auth::user()->school_id;
        $userId = Auth::id();

        $fromTermId = $request->from_term_id;
        $toTermId = $request->to_term_id;

    // Fetch both terms
    $fromTerm = Term::where('school_id', $schoolId)->findOrFail($fromTermId);
    $toTerm   = Term::where('school_id', $schoolId)->findOrFail($toTermId);

    // ✅ 1. Prevent same-term promotion
    if ($fromTermId == $toTermId) {
        return back()->with('error', 'You cannot promote to the same term.');
    }

    // ✅ 2. Prevent backward promotion (e.g., Term 3 → Term 2)
    if ($toTerm->start_date < $fromTerm->start_date) {
        return back()->with('error', 'You cannot promote to a previous term.');
    }

    // ✅ 3. Check term sequence (strict mode)
    $terms = Term::where('school_id', $schoolId)
        ->orderBy('start_date')
        ->pluck('id')
        ->toArray();

    $fromIndex = array_search($fromTermId, $terms);
    $toIndex = array_search($toTermId, $terms);

    if ($toIndex !== $fromIndex + 1) {
        return back()->with('error', 'Invalid promotion: you can only promote to the next term in sequence.');
    }

    // ✅ 4. Prevent duplicate promotion runs
    $alreadyRun = PromotionRun::where('school_id', $schoolId)
        ->where('from_term_id', $fromTermId)
        ->where('to_term_id', $toTermId)
        ->exists();

    if ($alreadyRun) {
        return back()->with('error', 'Promotion for these terms has already been executed.');
    }

     $studentsCount = Student::where('school_id', $schoolId)->count();

    if ($studentsCount === 0) {
        return back()->with('error', 'No students found. Please add students before running term promotion.');
    }

        

        $this->promotionService->promoteToNextTerm(
            $schoolId,
            $request->from_term_id,
            $request->to_term_id,
            $userId
        );

        return back()->with('success', 'Students promoted to next term successfully!');
    }

    /**
     * Promote students to next class (end of academic year)
     */
   public function promoteToNextClass(Request $request)
{
    $schoolId = Auth::user()->school_id;
    $userId = Auth::id();

    $activeTerm = Term::where('school_id', $schoolId)
        ->where('active', true)
        ->first();

    if (!$activeTerm) {
        return back()->with('error', 'No active term found for the current year.');
    }

    $studentsCount = Student::where('school_id', $schoolId)->count();

    if ($studentsCount === 0) {
    return back()->with('error', 'No students found. Please add students before running class promotion.');
   }

    // ✅ Check if this is the final term of the academic year
    $termsInYear = Term::where('school_id', $schoolId)
        ->where('year', $activeTerm->year)
        ->orderBy('start_date')
        ->get();

    $lastTerm = $termsInYear->last();

    if ($activeTerm->id !== $lastTerm->id) {
        return back()->with('error', 'You can only promote to the next class after the final term of the year.');
    }

    // ✅ Check if next year's first term exists
    $nextYearFirstTerm = Term::where('school_id', $schoolId)
        ->where('year', $activeTerm->year + 1)
        ->orderBy('start_date', 'asc')
        ->first();

    if (!$nextYearFirstTerm) {
        return redirect()->route('addterm')
            ->with('error', 'Next academic year’s first term not found. Please create it first before promoting.');
    }

    // ✅ If all checks pass → run the promotion
    $this->promotionService->promoteToNextClass($schoolId, $activeTerm->year, $userId);

    return back()->with('success', 'Students promoted to next class successfully!');
}

}
