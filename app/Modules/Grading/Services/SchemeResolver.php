<?php

namespace App\Modules\Grading\Services;

use App\Models\AcademicYear;
use App\Models\GradeScale;
use App\Models\GradeScaleBand;
use App\Models\GradingScheme;
use App\Models\SchoolGradingSetting;
use Illuminate\Support\Facades\DB;

class SchemeResolver
{
    public function schemeForSchoolYear(int $schoolId, int $academicYearId): ?GradingScheme
    {
        $setting = SchoolGradingSetting::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->first();

        if (! $setting) {
            return null;
        }

        return GradingScheme::query()->find($setting->grading_scheme_id);
    }

    public function schemeForTerm(int $schoolId, int $termId): ?GradingScheme
    {
        $yearId = AcademicYear::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereHas('terms', fn ($q) => $q->withoutGlobalScopes()->where('terms.id', $termId))
            ->value('id');

        if (! $yearId) {
            $yearId = DB::table('terms')->where('id', $termId)->where('school_id', $schoolId)->value('academic_year_id');
        }

        if (! $yearId) {
            return null;
        }

        return $this->schemeForSchoolYear($schoolId, (int) $yearId);
    }

    public function ensureDefaultScale(int $schoolId, GradingScheme $scheme): GradeScale
    {
        $existing = GradeScale::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('grading_scheme_id', $scheme->id)
            ->where('is_default', true)
            ->first();

        if ($existing) {
            return $existing;
        }

        $scale = GradeScale::createForSchool($schoolId, [
            'grading_scheme_id' => $scheme->id,
            'name' => $scheme->name.' default',
            'is_default' => true,
        ]);

        if ($scheme->isCbc()) {
            $bands = [
                ['label' => 'Exceeding Expectations', 'code' => 'EE', 'min_score' => null, 'max_score' => null, 'gpa_points' => null, 'sort_order' => 1],
                ['label' => 'Meeting Expectations', 'code' => 'ME', 'min_score' => null, 'max_score' => null, 'gpa_points' => null, 'sort_order' => 2],
                ['label' => 'Approaching Expectations', 'code' => 'AE', 'min_score' => null, 'max_score' => null, 'gpa_points' => null, 'sort_order' => 3],
                ['label' => 'Below Expectations', 'code' => 'BE', 'min_score' => null, 'max_score' => null, 'gpa_points' => null, 'sort_order' => 4],
            ];
        } else {
            $bands = collect($scheme->config['default_scale'] ?? [])->map(fn ($row) => [
                'label' => $row['label'],
                'code' => $row['code'],
                'min_score' => $row['min'],
                'max_score' => $row['max'],
                'gpa_points' => $row['gpa'],
                'sort_order' => $row['sort'],
            ])->all();
        }

        foreach ($bands as $band) {
            GradeScaleBand::query()->create(array_merge($band, [
                'grade_scale_id' => $scale->id,
            ]));
        }

        return $scale->load('bands');
    }

    public function usesCompetency(?GradingScheme $scheme): bool
    {
        return $scheme?->isCbc() ?? false;
    }
}
