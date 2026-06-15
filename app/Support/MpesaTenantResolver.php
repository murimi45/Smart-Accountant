<?php

namespace App\Support;

use App\Models\PaymentChannel;
use App\Models\Student;
use Illuminate\Http\Request;

class MpesaTenantResolver
{
    public static function extractShortCode(Request $request): ?string
    {
        $code = $request->input('BusinessShortCode')
            ?? $request->input('business_short_code');

        return $code !== null && $code !== '' ? trim((string) $code) : null;
    }

    public static function extractAdmission(Request $request): ?string
    {
        $adm = $request->input('BillRefNumber');

        return $adm !== null && $adm !== '' ? trim((string) $adm) : null;
    }

    /** Resolve paybill/till → school via active payment channel (no auth / no global scopes). */
    public static function resolveChannel(Request $request): ?PaymentChannel
    {
        $shortcode = self::extractShortCode($request);

        if (! $shortcode) {
            return null;
        }

        return PaymentChannel::findActiveByIdentifier($shortcode);
    }

    public static function findStudentInSchool(int $schoolId, string $admission): ?Student
    {
        return Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('admission', $admission)
            ->first();
    }
}
