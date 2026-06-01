<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Term;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Invoice;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ENROLLMENT SCREEN — main listing
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $activeTerm = Term::current1($schoolId);
        $nextTerm = Term::nextInYear($schoolId, $activeTerm);

        $termsOrdered = Term::orderedForSchool($schoolId);
        $termNextMap = self::buildTermNextMap($termsOrdered);
        $termLabels = [];
        foreach ($termsOrdered as $term) {
            $termLabels[$term->id] = $term->name . ' — ' . ($term->year ?? '');
        }

        $promotionFromTermId = $activeTerm?->id;
        $promotionNextTerm = $promotionFromTermId
            ? Term::nextInYear($schoolId, $activeTerm)
            : null;

        // Term being viewed — admin can switch via filter, defaults to active
        $termId = $request->filled('term_id') ? $request->term_id : $activeTerm?->id;

        // 2. Build the enrollment query using scopes
        $query = StudentEnrollment::with([
                'student',                          // student name, admission
                'schoolClass',                      // class name, level, is_final
                'stream',                           // stream name e.g. "A"
                'term',                             // term name
                'invoice',                          // linked invoice (enrollment_id FK)
            ])
            
            ->forTerm($termId)
            ->visible()                             // excludes cancelled (audit-only)
            ->search($request->search)
            ->when($request->filled('class_id'), fn($q) => $q->forClass($request->class_id))
            ->when($request->filled('status'),   fn($q) => $q->forStatus($request->status));

        // 3. Paginate — keeps large schools fast
        $enrollments = $query
            ->orderBy(
                // Sort by class level so Class 1 appears before Class 8
                Classes::select('order')
                    ->whereColumn('classes.id', 'student_enrollments.class_id')
                    ->limit(1)
            )
            ->orderBy('student_id')     // secondary sort: consistent ordering within class
            ->paginate(25)
            ->withQueryString();        // keeps filters active across pages

        // 4. School-wide counts for the header stats bar
        //    Always counts the whole school regardless of filters active
        $counts = StudentEnrollment::forTerm($termId)
            ->visible()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active'           THEN 1 ELSE 0 END) as promoting,
                SUM(CASE WHEN status = 'repeating'        THEN 1 ELSE 0 END) as repeating,
                SUM(CASE WHEN status = 'inactive'         THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN status = 'wrongly_promoted' THEN 1 ELSE 0 END) as needs_correction
            ")
            ->first();

        // 5. Sidebar data for filter dropdowns
        $classes = Classes::where('school_id', $schoolId)
            ->orderBy('order')
            ->get();

        $terms = Term::where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();


        $currentAcademicYear = $activeTerm?->academicYear;
        $academicYears = AcademicYear::after($schoolId, $currentAcademicYear)->get();
        $nextAcademicYear = AcademicYear::nextAfter($schoolId, $currentAcademicYear);

        return view('promotion.enrollment', compact(
            'enrollments',
            'counts',
            'classes',
            'terms',
            'termsOrdered',
            'termNextMap',
            'termLabels',
            'activeTerm',
            'termId',
            'academicYears',
            'nextTerm',
            'promotionFromTermId',
            'promotionNextTerm',
            'currentAcademicYear',
            'nextAcademicYear'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS UPDATE — when admin changes dropdown on a single row
    | Called via form POST (or AJAX later)
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, int $enrollmentId)
    {
        $request->validate([
            'status' => 'required|in:active,repeating,inactive',
        ]);

        $schoolId   = Auth::user()->school_id;
        $enrollment = StudentEnrollment::findOrFail($enrollmentId);

        // If the student already has an active enrollment for this term
        // and admin is trying to change it → flag as wrongly_promoted instead
        // of a silent save. This is the correction trigger.
        $newStatus = $request->status;

        $canChangeAfterPromotion =
            $newStatus === StudentEnrollment::STATUS_INACTIVE
            || (
                $enrollment->status === StudentEnrollment::STATUS_INACTIVE
                && in_array($newStatus, [
                    StudentEnrollment::STATUS_ACTIVE,
                    StudentEnrollment::STATUS_REPEATING,
                ], true)
            );

        if (
            $enrollment->promoted_from_enrollment_id
            && $newStatus !== $enrollment->status
            && ! $canChangeAfterPromotion
        ) {
            $enrollment->update(['status' => StudentEnrollment::STATUS_WRONGLY_PROMOTED]);

            return redirect()->back()->with(
                'warning',
                'This student was already promoted. Use the Correct button to fix their enrollment.'
            );
        }

        $enrollment->update(['status' => $newStatus]);

        $message = $newStatus === StudentEnrollment::STATUS_INACTIVE
            ? 'Student marked inactive. Their invoice for this term has been voided.'
            : 'Status updated.';

        return redirect()->back()->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | CORRECTION — open the correction modal with the right data
    | GET — returns data for the modal (used if you build an AJAX endpoint)
    |--------------------------------------------------------------------------
    */

    public function showCorrection(int $enrollmentId)
    {
        $schoolId   = Auth::user()->school_id;
        $enrollment = StudentEnrollment::with(['student', 'schoolClass', 'stream', 'invoice'])
            
            ->findOrFail($enrollmentId);

        // Available classes for the correction dropdown
        $classes = Classes::where('school_id', $schoolId)
            ->orderBy('order')
            ->get();

        // Available streams
        $streams = \App\Models\Stream::where('school_id', $schoolId)->get();

        return response()->json([
            'enrollment'    => $enrollment,
            'student'       => $enrollment->student,
            'wrong_class'   => $enrollment->schoolClass?->name,
            'wrong_stream'  => $enrollment->stream?->name,
            'invoice_id'    => $enrollment->invoice?->id,
            'classes'       => $classes,
            'streams'       => $streams,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CORRECTION — execute the correction
    | POST — cancels wrong enrollment, creates correct one
    |--------------------------------------------------------------------------
    */

    public function executeCorrection(Request $request, int $enrollmentId)
    {
        $request->validate([
            'correct_class_id'  => 'required|exists:classes,id',
            'correct_stream_id' => 'nullable|exists:streams,id',
            'correction_reason' => 'required|string|max:500',
        ]);

        $schoolId = Auth::user()->school_id;

        $wrongEnrollment = StudentEnrollment::with(['invoice', 'student'])
           
            ->findOrFail($enrollmentId);

        DB::transaction(function () use ($wrongEnrollment, $request, $schoolId) {

            $priorEnrollmentId = $wrongEnrollment->promoted_from_enrollment_id;

            // 1. Cancel the wrong enrollment (keep it — audit record).
            //    Clear promoted_from so the unique slot is free for the corrected row.
            $wrongEnrollment->update([
                'status'                      => StudentEnrollment::STATUS_CANCELLED,
                'correction_reason'           => $request->correction_reason,
                'promoted_from_enrollment_id' => null,
            ]);

            // 2. Void the invoice attached to the wrong enrollment
            if ($wrongEnrollment->invoice) {
                $wrongEnrollment->invoice->update([
                    'status' => 'voided',
                    'notes'  => 'Voided — enrollment corrected. Reason: ' . $request->correction_reason,
                ]);
            }

            // 3. Create the corrected enrollment — inherits the prior-term link
            $correctedEnrollment = StudentEnrollment::create([
                'school_id'                   => $schoolId,
                'student_id'                  => $wrongEnrollment->student_id,
                'class_id'                    => $request->correct_class_id,
                'stream_id'                   => $request->correct_stream_id
                                                    ?? $wrongEnrollment->stream_id,
                'term_id'                     => $wrongEnrollment->term_id,
                'status'                      => StudentEnrollment::STATUS_ACTIVE,
                'promoted_from_enrollment_id' => $priorEnrollmentId,
                'correction_reason'           => $request->correction_reason,
            ]);

            // 4. New invoice is auto-created by EnrollmentObserver
            //    (triggers createOrUpdateInvoice on created event)
            //    Nothing extra to do here — the observer handles it.

        });

        return redirect()->back()->with('success',
            'Enrollment corrected successfully. A new invoice has been generated.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BULK STATUS SAVE (optional — for future "save all changes" button)
    | POST — receives array of enrollment_id => status pairs
    |--------------------------------------------------------------------------
    */

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'statuses'   => 'required|array',
            'statuses.*' => 'required|in:active,repeating,inactive',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->statuses as $enrollmentId => $status) {
                StudentEnrollment::where('id', $enrollmentId)
                    ->whereNull('promoted_from_enrollment_id') // only pre-promotion rows
                    ->update(['status' => $status]);
            }
        });

        return redirect()->back()->with('success', 'Enrollment statuses updated.');
    }

    /**
     * Valid term-to-term successors: same academic year, term_number + 1 only.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Term>  $terms
     * @return array<int, int> from_term_id => to_term_id
     */
    private static function buildTermNextMap($terms): array
    {
        $map = [];

        foreach ($terms->groupBy('academic_year_id') as $yearTerms) {
            $byNumber = $yearTerms->keyBy('term_number');

            foreach ($yearTerms as $term) {
                $successor = $byNumber->get($term->term_number + 1);

                if (
                    $successor
                    && $successor->start_date > $term->start_date
                ) {
                    $map[$term->id] = $successor->id;
                }
            }
        }

        return $map;
    }
}