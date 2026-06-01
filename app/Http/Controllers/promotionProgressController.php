<?php

namespace App\Http\Controllers;

use App\Models\PromotionRun;
use App\Models\StudentEnrollment;
use App\Models\Classes;
use Illuminate\Support\Facades\Auth;

class PromotionProgressController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PROGRESS PAGE
    |--------------------------------------------------------------------------
    */
    public function show(int $promotionRunId)
    {
        $schoolId = Auth::user()->school_id;

        $run = PromotionRun::where('school_id', $schoolId)
            ->with(['fromTerm', 'toTerm', 'promotedBy'])
            ->findOrFail($promotionRunId);

        $classes = Classes::where('school_id', $schoolId)
            ->orderBy('order')
            ->get();

        return view('promotion.promotionprogress', compact('run', 'classes'));
    }

    /*
    |--------------------------------------------------------------------------
    | POLL ENDPOINT
    | Called every 2 seconds by the blade JS.
    | Checks run status first — handles pending/failed before counting enrollments.
    |--------------------------------------------------------------------------
    */
    public function poll(int $promotionRunId)
    {
        $schoolId = Auth::user()->school_id;

        $run = PromotionRun::where('school_id', $schoolId)
            ->findOrFail($promotionRunId);

        // --- Job not picked up yet ---
        if ($run->status === 'pending') {
            return response()->json([
                'state'           => 'pending',
                'pct'             => 0,
                'total_expected'  => 0,
                'total_processed' => 0,
                'skipped'         => 0,
                'errors'          => 0,
                'batches'         => [],
                'message'         => 'Promotion is queued and will start shortly...',
            ]);
        }

        // --- Job failed before completing ---
        if ($run->status === 'failed') {
            return response()->json([
                'state'           => 'failed',
                'pct'             => 0,
                'total_expected'  => 0,
                'total_processed' => 0,
                'skipped'         => 0,
                'errors'          => 0,
                'batches'         => [],
                'message'         => $run->error_message ?? 'Promotion failed. Please contact support.',
            ]);
        }

        // --- Running or completed — count real enrollment progress ---
        $classes = Classes::where('school_id', $schoolId)
            ->orderBy('order')
            ->get();

        $totalExpected = StudentEnrollment::where('term_id', $run->from_term_id)
            ->whereIn('status', [
                StudentEnrollment::STATUS_ACTIVE,
                StudentEnrollment::STATUS_REPEATING,
            ])
            ->count();

        $totalProcessed = StudentEnrollment::where('term_id', $run->to_term_id)
            ->whereNotNull('promoted_from_enrollment_id')
            ->count();

        $skipped = StudentEnrollment::where('term_id', $run->from_term_id)
            ->where('status', StudentEnrollment::STATUS_INACTIVE)
            ->count();

        $errors = StudentEnrollment::where('term_id', $run->to_term_id)
            ->where('status', StudentEnrollment::STATUS_WRONGLY_PROMOTED)
            ->count();

        $batches = $classes->map(function ($class) use ($run) {

            $expected = StudentEnrollment::where('term_id', $run->from_term_id)
                ->where('class_id', $class->id)
                ->whereIn('status', [
                    StudentEnrollment::STATUS_ACTIVE,
                    StudentEnrollment::STATUS_REPEATING,
                ])
                ->count();

            $done = StudentEnrollment::where('term_id', $run->to_term_id)
                ->whereNotNull('promoted_from_enrollment_id')
                ->whereHas('promotedFrom', function ($q) use ($class) {
                    $q->where('class_id', $class->id);
                })
                ->count();

            $failed = StudentEnrollment::where('term_id', $run->to_term_id)
                ->where('status', StudentEnrollment::STATUS_WRONGLY_PROMOTED)
                ->whereHas('promotedFrom', function ($q) use ($class) {
                    $q->where('class_id', $class->id);
                })
                ->count();

            if ($expected === 0) {
                $state = 'empty';
            } elseif ($done >= $expected) {
                $state = $failed > 0 ? 'error' : 'done';
            } elseif ($done > 0) {
                $state = 'active';
            } else {
                $state = 'waiting';
            }

            return [
                'class_id'   => $class->id,
                'class_name' => $class->name,
                'is_final'   => $class->is_final,
                'expected'   => $expected,
                'done'       => $done,
                'failed'     => $failed,
                'state'      => $state,
            ];

        })->filter(fn($b) => $b['expected'] > 0)->values();

        $pct = $totalExpected > 0
            ? round(($totalProcessed / $totalExpected) * 100)
            : 0;

        // Trust the run status for completion — avoids race condition
        // where processed count equals expected before DB commit finishes
        $overallState = match($run->status) {
            'completed' => $errors > 0 ? 'error' : 'done',
            'failed'    => 'failed',
            default     => 'running',
        };

        return response()->json([
            'state'           => $overallState,
            'total_expected'  => $totalExpected,
            'total_processed' => $totalProcessed,
            'skipped'         => $skipped,
            'errors'          => $errors,
            'pct'             => $pct,
            'batches'         => $batches,
            'promotion_type'  => $run->type,
            'from_term' => $run->fromTerm ? $run->fromTerm->name : '',
            'to_term'   => $run->toTerm   ? $run->toTerm->name   : '',
        ]);
    }
}