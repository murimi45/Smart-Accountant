<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassSubject;
use App\Models\TermReportCard;
use Illuminate\View\View;

class GradingDashboardController extends Controller
{
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;

        $openAssessments = Assessment::query()
            ->where('status', Assessment::STATUS_OPEN)
            ->count();

        $draftCards = TermReportCard::query()
            ->where('status', TermReportCard::STATUS_DRAFT)
            ->count();

        $publishedCards = TermReportCard::query()
            ->where('status', TermReportCard::STATUS_PUBLISHED)
            ->count();

        $classSubjects = ClassSubject::query()->count();

        return view('grading::dashboard', compact(
            'openAssessments',
            'draftCards',
            'publishedCards',
            'classSubjects'
        ));
    }
}
