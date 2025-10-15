<?php

namespace App\Services;

use App\Models\{Student, Invoice, Term, PromotionRun, StudentHistory};
use Illuminate\Support\Facades\DB;
use Exception;


class PromotionService
{
    /**
     * Promote all students in a school to the next term
     * while carrying forward balances and credits.
     */
    public function promoteToNextTerm($schoolId, $fromTermId, $toTermId, $userId = null)
    {
        DB::transaction(function () use ($schoolId, $fromTermId, $toTermId, $userId) {

            // ✅ Record promotion run
            $promotionRun = PromotionRun::create([
                'school_id' => $schoolId,
                'from_term_id' => $fromTermId,
                'to_term_id' => $toTermId,
                'promoted_by' => $userId,
                'type' => 'term_promotion',
            ]);

            // ✅ Fetch all active students for this school
            $students = Student::where('school_id', $schoolId)->get();

            foreach ($students as $student) {

                // Get the previous term invoice
                $previousInvoice = Invoice::where('student_id', $student->id)
                    ->where('term_id', $fromTermId)
                    ->first();

                if (!$previousInvoice) {
                    // Skip students without invoice in the previous term
                    continue;
                }

                // Compute carried amounts
                $balanceForward = max($previousInvoice->balance, 0);   // unpaid amount
                $creditForward  = max(-$previousInvoice->balance, 0);  // overpayment

                // ✅ Create new invoice for the next term
                $newInvoice = Invoice::create([
                    'student_id' => $student->id,
                    'term_id' => $toTermId,
                    'total_amount' => $student->class->fee ?? 0,
                    'balance_forward' => $balanceForward,
                    'credit_forward' => $creditForward,
                ]);

                // ✅ Record the promotion in student history
                StudentHistory::create([
                    'student_id' => $student->id,
                    'from_class_id' => $student->class_id,
                    'to_class_id' => $student->class_id,
                    'from_term_id' => $fromTermId,
                    'to_term_id' => $toTermId,
                    'carried_balance' => $balanceForward,
                    'carried_credit' => $creditForward,
                ]);
            }

            Term::where('school_id', $schoolId)->update(['active' => false]);
            Term::where('id', $toTermId)->update(['active' => true]);
        });
    }

    /**
     * Promote students to the next class at the end of the academic year.
     */
    public function promoteToNextClass($schoolId, $academicYear, $userId = null)
{
    DB::transaction(function () use ($schoolId, $academicYear, $userId) {

        $activeTerm = Term::where('school_id', $schoolId)
            ->where('active', true)
            ->first();

        if (!$activeTerm) {
            throw new Exception('No active term found for the current year.');
        }

        // ✅ Find or create the next year's first term
        $nextYearFirstTerm = Term::where('school_id', $schoolId)
            ->where('year', $activeTerm->year + 1)
            ->orderBy('start_date', 'asc')
            ->first();

       if (!$nextYearFirstTerm) {
    return redirect()->back()->with('error', 'Next academic year’s first term not found. Please create it first before promoting.');
}


        // ✅ Log the class promotion run
        PromotionRun::create([
            'school_id' => $schoolId,
            'from_term_id' => $activeTerm->id,
            'to_term_id' => $nextYearFirstTerm->id,
            'promoted_by' => $userId,
            'type' => 'class_promotion',
        ]);

        // ✅ Get all students in this school
        $students = Student::where('school_id', $schoolId)->get();

        foreach ($students as $student) {
            $currentClass = $student->class;

            if (!$currentClass || !$currentClass->next_class_id) {
                continue; // skip if no next class defined
            }

            // 🔹 Get latest invoice for the active term
            $previousInvoice = Invoice::where('student_id', $student->id)
                ->where('term_id', $activeTerm->id)
                ->latest('id')
                ->first();

            $balanceForward = 0;
            $creditForward = 0;

            if ($previousInvoice) {
                $balanceForward = max($previousInvoice->balance, 0);
                $creditForward = max(-$previousInvoice->balance, 0);
            }

            // 🔹 Move student to the next class and term
            $student->update([
                'class_id' => $currentClass->next_class_id,
                'term_id' => $nextYearFirstTerm->id,
            ]);

            // 🔹 Create new invoice for next year's first term
            $nextClass = $student->fresh()->class; // refresh to get new class fee
            Invoice::create([
                'student_id' => $student->id,
                'term_id' => $nextYearFirstTerm->id,
                'total_amount' => $nextClass->fee ?? 0,
                'balance_forward' => $balanceForward,
                'credit_forward' => $creditForward,
            ]);

            // 🔹 Record promotion history
            StudentHistory::create([
                'student_id' => $student->id,
                'from_class_id' => $currentClass->id,
                'to_class_id' => $currentClass->next_class_id,
                'from_term_id' => $activeTerm->id,
                'to_term_id' => $nextYearFirstTerm->id,
                'carried_balance' => $balanceForward,
                'carried_credit' => $creditForward,
            ]);
        }

        // ✅ Switch term activation
        Term::where('school_id', $schoolId)->update(['active' => false]);
        $nextYearFirstTerm->update(['active' => true]);
    });
}

}
