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

                 $student->update([
                'term_id' => $toTermId,
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


public function promoteToNextClass($schoolId, $fromTerm, $toTerm, $userId = null)
{
    DB::transaction(function () use ($schoolId, $fromTerm, $toTerm, $userId) {
        
        // Verify the target term exists
        if (!$toTerm) {
            throw new \Exception('Target term not found.');
        }

        // Log the class promotion run
        PromotionRun::create([
            'school_id' => $schoolId,
            'from_term_id' => $fromTerm->id,
            'to_term_id' => $toTerm->id,
            'promoted_by' => $userId,
            'type' => 'class_promotion',
        ]);

        // Get all students in this school
        $students = Student::where('school_id', $schoolId)->get();
        $promotedCount = 0;

        foreach ($students as $student) {
            $currentClass = $student->class;

            if (!$currentClass || !$currentClass->next_class_id) {
                continue; // Skip if no next class defined
            }

            // Get latest invoice for the from term
            $previousInvoice = Invoice::where('student_id', $student->id)
                ->where('term_id', $fromTerm->id)
                ->latest('id')
                ->first();

            $balanceForward = 0;
            $creditForward = 0;

            if ($previousInvoice) {
                $balanceForward = max($previousInvoice->balance, 0);
                $creditForward = max(-$previousInvoice->balance, 0);
            }

            // Move student to the next class and term
            $student->update([
                'class_id' => $currentClass->next_class_id,
                'term_id' => $toTerm->id,
            ]);

            // Create new invoice for next year's first term
            $nextClass = $student->fresh()->class;
            Invoice::create([
                'student_id' => $student->id,
                'term_id' => $toTerm->id,
                'total_amount' => $nextClass->fee ?? 0,
                'balance_forward' => $balanceForward,
                'credit_forward' => $creditForward,
                'balance' => ($nextClass->fee ?? 0) + $balanceForward - $creditForward,
            ]);

            // Record promotion history
            StudentHistory::create([
                'student_id' => $student->id,
                'from_class_id' => $currentClass->id,
                'to_class_id' => $currentClass->next_class_id,
                'from_term_id' => $fromTerm->id,
                'to_term_id' => $toTerm->id,
                'carried_balance' => $balanceForward,
                'carried_credit' => $creditForward,
            ]);

            $promotedCount++;
        }

        // Switch term activation
        // Switch term activation
Term::where('school_id', $schoolId)->update(['active' => false]);
Term::where('id', $toTerm->id)->update(['active' => true]);


        if ($promotedCount === 0) {
            throw new \Exception('No students were promoted. Check class configurations.');
        }
    });
}
/*******  ad54016e-78e6-4af2-9c25-3a7d6dfd9219  *******/
}