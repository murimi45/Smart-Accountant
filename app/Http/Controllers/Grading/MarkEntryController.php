<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\View\View;

class MarkEntryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Assessment::class);

        $assessments = Assessment::query()
            ->with(['schoolClass', 'subject', 'term', 'assessmentType'])
            ->where('status', Assessment::STATUS_OPEN)
            ->orderByDesc('id')
            ->get();

        return view('grading::mark-entry.index', compact('assessments'));
    }

    public function show(Assessment $assessment): View
    {
        $this->authorize('view', $assessment);

        return view('grading::mark-entry.show', compact('assessment'));
    }
}
