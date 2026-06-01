<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Jobs\RunPromotion;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\PromotionRun;
use App\Services\PromotionService;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PROMOTE TO NEXT TERM
    |--------------------------------------------------------------------------
    */
    public function promoteToNextTerm(Request $request)
    {
        $request->validate([
            "from_term_id" => "required|exists:terms,id",
            "to_term_id" => "required|exists:terms,id",
        ]);

        $schoolId = Auth::user()->school_id;
        $userId = Auth::id();
        $fromTermId = $request->from_term_id;
        $toTermId = $request->to_term_id;

        $fromTerm = Term::where("school_id", $schoolId)->findOrFail(
            $fromTermId
        );
        $toTerm = Term::where("school_id", $schoolId)->findOrFail($toTermId);

        if ($fromTermId == $toTermId) {
            return back()->with(
                "error",
                "You cannot promote to the same term."
            );
        }

        if ($toTerm->start_date < $fromTerm->start_date) {
            return back()->with(
                "error",
                "You cannot promote to a previous term."
            );
        }

        if ((int) $toTerm->academic_year_id !== (int) $fromTerm->academic_year_id) {
            return back()->with(
                "error",
                "Term promotion must stay within the same academic year. Use \"Promote to next year\" for the next academic year."
            );
        }

        $expectedNext = Term::nextInYear($schoolId, $fromTerm);

        if (! $expectedNext || (int) $toTermId !== (int) $expectedNext->id) {
            return back()->with(
                "error",
                "Invalid promotion: you can only promote to the next term in this academic year (Term "
                . $fromTerm->term_number . " → Term " . ($fromTerm->term_number + 1) . ")."
            );
        }

        $enrollmentCount = StudentEnrollment::where("term_id", $fromTermId)
            ->whereIn("status", [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->count();

        if ($enrollmentCount === 0) {
            return back()->with(
                "error",
                "No active enrollments found for this term."
            );
        }

        try {
            $run = $this->startPromotionRun(
                $schoolId,
                $fromTermId,
                $toTermId,
                $userId,
                "term_promotion"
            );
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                "error",
                "Unable to start term promotion. Please try again or contact support."
            );
        }

        RunPromotion::dispatch(
            $run->id,
            $schoolId,
            $fromTermId,
            $toTermId,
            $userId,
            "term"
        );

        return redirect()->route("promotion.progress", $run->id);
    }

    /*
    |--------------------------------------------------------------------------
    | PROMOTE TO NEXT CLASS (YEAR PROMOTION)
    |--------------------------------------------------------------------------
    */
    public function promoteToNextClass(Request $request)
    {
        $request->validate([
            "academic_year" => "required|string|max:50",
        ]);

        $schoolId = Auth::user()->school_id;
        $userId = Auth::id();
        $targetYearName = (string) $request->academic_year;

        $activeTerm = Term::current1($schoolId);

        if (! $activeTerm) {
            return back()->with(
                "error",
                "No terms found. Please create terms first."
            );
        }

        $currentAcademicYear = $activeTerm->academicYear;

        if (! $currentAcademicYear) {
            return back()->with(
                "error",
                "The active term has no academic year assigned."
            );
        }

        $enrollmentCount = StudentEnrollment::where("term_id", $activeTerm->id)
            ->whereIn("status", [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->count();

        if ($enrollmentCount === 0) {
            return back()->with(
                "error",
                "No active enrollments found for this term."
            );
        }

        $lastTermOfYear = Term::where("school_id", $schoolId)
            ->where("academic_year_id", $activeTerm->academic_year_id)
            ->orderByDesc("start_date")
            ->first();

        if ($activeTerm->id !== $lastTermOfYear->id) {
            return back()->with(
                "error",
                "You can only promote to the next class after the final term of the year."
            );
        }

        $expectedNextYear = AcademicYear::nextAfter($schoolId, $currentAcademicYear);

        if (! $expectedNextYear) {
            return redirect()
                ->route("addterm")
                ->with(
                    "error",
                    "No next academic year found after {$currentAcademicYear->name}. Please create it first."
                );
        }

        $targetAcademicYear = AcademicYear::where("school_id", $schoolId)
            ->where("name", $targetYearName)
            ->first();

        if (! $targetAcademicYear) {
            return redirect()
                ->route("addterm")
                ->with(
                    "error",
                    "Academic year {$targetYearName} not found. Please create it first."
                );
        }

        if ($targetAcademicYear->start_date <= $currentAcademicYear->start_date) {
            return back()->with(
                "error",
                "You cannot promote backward. Students are in {$currentAcademicYear->name}; choose a later academic year."
            );
        }

        if ($targetAcademicYear->id !== $expectedNextYear->id) {
            return back()->with(
                "error",
                "You can only promote to the next academic year ({$expectedNextYear->name}), not {$targetAcademicYear->name}."
            );
        }

        $nextYearFirstTerm = Term::where("school_id", $schoolId)
            ->where("academic_year_id", $targetAcademicYear->id)
            ->orderBy("start_date")
            ->first();

        if (!$nextYearFirstTerm) {
            return redirect()
                ->route("addterm")
                ->with(
                    "error",
                    "Next academic year's first term ({$targetYearName}) not found. Please create it first."
                );
        }

        $configError = PromotionService::validateClassPromotionConfig(
            $schoolId,
            $activeTerm->id
        );

        if ($configError) {
            return back()->with("error", $configError);
        }

        try {
            $run = $this->startPromotionRun(
                $schoolId,
                $activeTerm->id,
                $nextYearFirstTerm->id,
                $userId,
                "class_promotion"
            );
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                "error",
                "Unable to start year promotion. Please try again or contact support."
            );
        }

        RunPromotion::dispatch(
            $run->id,
            $schoolId,
            $activeTerm->id,
            $nextYearFirstTerm->id,
            $userId,
            "class"
        );

        return redirect()->route("promotion.progress", $run->id);
    }

    private function startPromotionRun(
        int $schoolId,
        int $fromTermId,
        int $toTermId,
        int $userId,
        string $type
    ): PromotionRun {
        $activeKey = PromotionRun::activeKey(
            $schoolId,
            $fromTermId,
            $toTermId,
            $type
        );

        try {
            return DB::transaction(function () use (
                $schoolId,
                $fromTermId,
                $toTermId,
                $userId,
                $type,
                $activeKey
            ) {
                // Lock any existing row for this slot (including failed with null key — optional)
                $blocking = PromotionRun::where("active_key", $activeKey)
                    ->lockForUpdate()
                    ->first();

                if ($blocking) {
                    throw new \RuntimeException(
                        "Promotion for these terms has already been executed."
                    );
                }

                return PromotionRun::create([
                    "school_id" => $schoolId,
                    "from_term_id" => $fromTermId,
                    "to_term_id" => $toTermId,
                    "promoted_by" => $userId,
                    "type" => $type,
                    "status" => "pending",
                    "active_key" => $activeKey,
                ]);
            });
        } catch (QueryException $e) {
            // 1062 = MySQL duplicate entry (second admin won the race)
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                throw new \RuntimeException(
                    "Promotion for these terms has already been executed."
                );
            }

            report($e);

            throw new \RuntimeException(
                "Unable to start promotion due to a database error. Please contact support."
            );
        }
    }
}
