<?php

namespace App\Services;

use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\PromotionRun;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    private const CHUNK_SIZE = 100;

    /**
     * Pre-flight check before year promotion is queued.
     * Returns an error message, or null if configuration is valid.
     */
    public static function validateClassPromotionConfig(int $schoolId, int $fromTermId): ?string
    {
        $issues = self::collectClassPromotionConfigIssues($schoolId, $fromTermId);

        if ($issues === []) {
            return null;
        }

        return 'Cannot promote to next year: ' . implode('; ', $issues) . '.';
    }

    /*
    |--------------------------------------------------------------------------
    | PROMOTE TO NEXT TERM
    |--------------------------------------------------------------------------
    */
    public function promoteToNextTerm(
        int $promotionRunId,
        int $schoolId,
        int $fromTermId,
        int $toTermId,
        ?int $userId = null
    ): void {
        try {
            $this->chunkSourceEnrollments($fromTermId, function ($enrollments) use (
                $schoolId,
                $toTermId
            ) {
                DB::transaction(function () use ($enrollments, $schoolId, $toTermId) {
                    foreach ($enrollments as $enrollment) {
                        $this->promoteEnrollment(
                            $enrollment,
                            $schoolId,
                            $toTermId,
                            $enrollment->class_id,
                            $enrollment->status
                        );
                    }
                });
            });

            $this->activateTermPromotion($schoolId, $toTermId);
            $this->generateInvoicesForPromotedTerm($toTermId);

            PromotionRun::where('id', $promotionRunId)
                ->update(['status' => 'completed']);

        } catch (\Throwable $e) {
            PromotionRun::where('id', $promotionRunId)->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'active_key'    => null,
            ]);

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROMOTE TO NEXT CLASS (YEAR PROMOTION)
    |--------------------------------------------------------------------------
    */
    public function promoteToNextClass(
        int $promotionRunId,
        int $schoolId,
        Term $fromTerm,
        Term $toTerm,
        ?int $userId = null
    ): void {
        if (!$toTerm) {
            throw new \Exception('Target term not found.');
        }

        try {
            $enrolledCount  = 0;
            $graduatedCount = 0;
            $skipped        = [];

            $this->chunkSourceEnrollments($fromTerm->id, function ($enrollments) use (
                $schoolId,
                $toTerm,
                &$enrolledCount,
                &$graduatedCount,
                &$skipped
            ) {
                DB::transaction(function () use (
                    $enrollments,
                    $schoolId,
                    $toTerm,
                    &$enrolledCount,
                    &$graduatedCount,
                    &$skipped
                ) {
                    foreach ($enrollments as $enrollment) {
                        $action = self::resolveClassPromotionAction($enrollment, $schoolId);

                        if ($action['action'] === 'graduate') {
                            $graduatedCount++;
                            continue;
                        }

                        if ($action['action'] === 'skip') {
                            $skipped[] = $action['message'];
                            continue;
                        }

                        $this->promoteEnrollment(
                            $enrollment,
                            $schoolId,
                            $toTerm->id,
                            $action['class_id'],
                            StudentEnrollment::STATUS_ACTIVE
                        );

                        $enrolledCount++;
                    }
                });
            });

            if ($skipped !== []) {
                throw new \Exception(self::formatClassPromotionSkips($skipped));
            }

            if ($enrolledCount === 0 && $graduatedCount === 0) {
                throw new \Exception('No students were promoted. Check class configurations.');
            }

            $this->activateYearPromotion($schoolId, $toTerm);
            $this->generateInvoicesForPromotedTerm($toTerm->id);

            PromotionRun::where('id', $promotionRunId)
                ->update(['status' => 'completed']);

        } catch (\Throwable $e) {
            PromotionRun::where('id', $promotionRunId)->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'active_key'    => null,
            ]);

            throw $e;
        }
    }

    /**
     * Process enrollments in chunks to limit transaction size and memory use.
     */
    private function chunkSourceEnrollments(int $fromTermId, callable $callback): void
    {
        StudentEnrollment::with(['student', 'schoolClass', 'stream'])
            ->where('term_id', $fromTermId)
            ->whereIn('status', [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($enrollments) use ($callback) {
                $callback($enrollments);
            });
    }

    private function activateTermPromotion(int $schoolId, int $toTermId): void
    {
        DB::transaction(function () use ($schoolId, $toTermId) {
            Term::where('school_id', $schoolId)->update(['active' => false]);
            Term::where('id', $toTermId)->update(['active' => true]);
        });
    }

    private function activateYearPromotion(int $schoolId, Term $toTerm): void
    {
        DB::transaction(function () use ($schoolId, $toTerm) {
            Term::where('school_id', $schoolId)->update(['active' => false]);
            Term::where('id', $toTerm->id)->update(['active' => true]);

            AcademicYear::where('school_id', $schoolId)->update(['is_current' => false]);
            AcademicYear::where('id', $toTerm->academic_year_id)->update(['is_current' => true]);
        });
    }

    /**
     * Create invoices after all enrollments exist (deferred from EnrollmentObserver during bulk promote).
     */
    private function generateInvoicesForPromotedTerm(int $toTermId): void
    {
        $invoiceService = app(InvoiceService::class);

        StudentEnrollment::with('student')
            ->where('term_id', $toTermId)
            ->whereNotNull('promoted_from_enrollment_id')
            ->whereIn('status', [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($enrollments) use ($invoiceService) {
                foreach ($enrollments as $enrollment) {
                    if (!$enrollment->student) {
                        continue;
                    }

                    $invoiceService->createOrUpdateInvoice(
                        $enrollment->student,
                        $enrollment->term_id,
                        $enrollment->id
                    );
                }
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Idempotent enrollment create — one promoted row per source enrollment
    |--------------------------------------------------------------------------
    */
    private function promoteEnrollment(
        StudentEnrollment $source,
        int $schoolId,
        int $toTermId,
        int $classId,
        string $status
    ): StudentEnrollment {
        return StudentEnrollment::withoutEvents(function () use (
            $source,
            $schoolId,
            $toTermId,
            $classId,
            $status
        ) {
            return StudentEnrollment::firstOrCreate(
                ['promoted_from_enrollment_id' => $source->id],
                [
                    'school_id'  => $schoolId,
                    'student_id' => $source->student_id,
                    'class_id'   => $classId,
                    'stream_id'  => $source->stream_id,
                    'term_id'    => $toTermId,
                    'status'     => $status,
                ]
            );
        });
    }

    /**
     * @return list<string>
     */
    private static function collectClassPromotionConfigIssues(int $schoolId, int $fromTermId): array
    {
        $issues = [];

        $enrollments = StudentEnrollment::with(['student', 'schoolClass'])
            ->where('term_id', $fromTermId)
            ->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->get();

        $classesMissingNext = [];

        foreach ($enrollments as $enrollment) {
            $class = $enrollment->schoolClass;

            if (!$class) {
                $issues[] = self::studentLabel($enrollment) . ' has no class assigned';
                continue;
            }

            if ($class->is_final) {
                continue;
            }

            if (!self::hasNextClass($schoolId, $class->order)) {
                $classesMissingNext[$class->name] = true;
            }
        }

        foreach (array_keys($classesMissingNext) as $className) {
            $issues[] = "no next class configured after \"{$className}\"";
        }

        return $issues;
    }

    /**
     * @return array{action: 'promote'|'graduate'|'skip', class_id?: int, message?: string}
     */
    private static function resolveClassPromotionAction(
        StudentEnrollment $enrollment,
        int $schoolId
    ): array {
        $class = $enrollment->schoolClass;

        if (!$class) {
            return [
                'action'  => 'skip',
                'message' => self::studentLabel($enrollment) . ' has no class assigned',
            ];
        }

        if ($enrollment->status === StudentEnrollment::STATUS_REPEATING) {
            return [
                'action'   => 'promote',
                'class_id' => $class->id,
            ];
        }

        if ($class->is_final) {
            return ['action' => 'graduate'];
        }

        $nextClass = Classes::where('school_id', $schoolId)
            ->where('order', $class->order + 1)
            ->first();

        if (!$nextClass) {
            return [
                'action'  => 'skip',
                'message' => self::studentLabel($enrollment)
                    . " in \"{$class->name}\" — no next class configured",
            ];
        }

        return [
            'action'   => 'promote',
            'class_id' => $nextClass->id,
        ];
    }

    private static function hasNextClass(int $schoolId, int $order): bool
    {
        return Classes::where('school_id', $schoolId)
            ->where('order', $order + 1)
            ->exists();
    }

    private static function studentLabel(StudentEnrollment $enrollment): string
    {
        $student = $enrollment->student;

        if ($student?->full_name) {
            $label = $student->full_name;
            if ($student->admission) {
                $label .= " ({$student->admission})";
            }

            return $label;
        }

        return 'Student #' . $enrollment->student_id;
    }

    /**
     * @param list<string> $skipped
     */
    private static function formatClassPromotionSkips(array $skipped): string
    {
        $unique = array_values(array_unique($skipped));
        $shown  = array_slice($unique, 0, 5);
        $message = 'Year promotion stopped — unresolved students: ' . implode('; ', $shown);

        $remaining = count($unique) - count($shown);
        if ($remaining > 0) {
            $message .= " (and {$remaining} more)";
        }

        return $message . '. Fix class order in Classes, then retry.';
    }
}
