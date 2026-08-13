<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\GradingScheme;
use App\Models\SchoolGradingSetting;
use App\Modules\Grading\Services\SchemeResolver;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingSettingsController extends Controller
{
    public function index(SchemeResolver $schemes): View
    {
        $this->authorize('viewAny', SchoolGradingSetting::class);

        $years = AcademicYear::query()->orderByDesc('is_current')->orderBy('name')->get();
        $catalog = GradingScheme::query()->orderBy('name')->get();
        $settings = SchoolGradingSetting::query()->with(['academicYear', 'gradingScheme'])->get()->keyBy('academic_year_id');

        return view('grading::settings.index', compact('years', 'catalog', 'settings'));
    }

    public function store(Request $request, SchemeResolver $schemes): RedirectResponse
    {
        $this->authorize('create', SchoolGradingSetting::class);

        $data = $request->validate([
            'academic_year_id' => ['required', TenantRules::academicYears()],
            'grading_scheme_id' => ['required', 'exists:grading_schemes,id'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $setting = SchoolGradingSetting::query()->updateOrCreate(
            ['academic_year_id' => $data['academic_year_id']],
            ['grading_scheme_id' => $data['grading_scheme_id']]
        );

        // BelongsToSchool may strip school_id on mass assign; ensure tenant key
        if (! $setting->school_id) {
            $setting->school_id = auth()->user()->school_id;
            $setting->save();
        }

        $scheme = GradingScheme::query()->findOrFail($data['grading_scheme_id']);
        $schemes->ensureDefaultScale((int) auth()->user()->school_id, $scheme);

        return redirect()->route('grading.settings.index')->with('success', 'Grading scheme saved for the academic year.');
    }
}
