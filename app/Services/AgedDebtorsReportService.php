<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AgedDebtorsReportService
{
    public const BUCKET_LABELS = [
        'current'      => 'Current (0–30 days)',
        'days_31_60'   => '31–60 days',
        'days_61_90'   => '61–90 days',
        'days_91_plus' => '91+ days',
    ];

    /**
     * @return array{
     *     rows: list<array<string, mixed>>,
     *     totals: array<string, float>,
     *     asOf: Carbon,
     *     debtorCount: int
     * }
     */
    public function build(int $schoolId, ?int $termId = null, ?int $classId = null, ?Carbon $asOf = null): array
    {
        $asOf = ($asOf ?? now())->copy()->startOfDay();

        $query = Invoice::query()
            ->where('school_id', $schoolId)
            ->where('balance', '>', 0)
            ->excludeVoided()
            ->excludeTransferred()
            ->with([
                'student',
                'term.academicYear',
                'enrollment.schoolClass',
                'enrollment.stream',
            ]);

        if ($termId) {
            $query->where('term_id', $termId);
        }

        if ($classId) {
            $query->whereHas('enrollment', static function ($q) use ($schoolId, $classId) {
                $q->where('school_id', $schoolId)->where('class_id', $classId);
            });
        }

        $invoices = $query->orderBy('invoice_date')->get();

        $grouped = $invoices->groupBy(static function (Invoice $invoice) use ($termId) {
            return $termId
                ? (string) $invoice->student_id
                : $invoice->student_id.'-'.$invoice->term_id;
        });

        $rows = $grouped->map(function (Collection $studentInvoices) use ($asOf) {
            /** @var Invoice $first */
            $first = $studentInvoices->first();
            $student = $first->student;
            $class = $first->enrollment?->schoolClass;
            $stream = $first->enrollment?->stream;
            $term = $first->term;

            $buckets = $this->emptyBuckets();
            $totalBalance = 0.0;
            $oldestDate = null;

            foreach ($studentInvoices as $invoice) {
                $balance = (float) $invoice->balance;
                $totalBalance += $balance;

                $invoiceDate = Carbon::parse($invoice->invoice_date)->startOfDay();
                if ($oldestDate === null || $invoiceDate->lt($oldestDate)) {
                    $oldestDate = $invoiceDate;
                }

                $days = (int) $invoiceDate->diffInDays($asOf, false);
                if ($days < 0) {
                    $days = 0;
                }

                $bucket = $this->bucketForDays($days);
                $buckets[$bucket] += $balance;
            }

            $classLabel = $class?->name ?? 'N/A';
            if ($stream?->name) {
                $classLabel .= ' ('.$stream->name.')';
            }

            return [
                'admission'    => $student?->admission ?? 'N/A',
                'student_name' => $student?->full_name ?? 'Unknown',
                'class_name'   => $classLabel,
                'term_name'    => $term ? trim($term->name.' '.($term->year ?? '')) : 'N/A',
                'invoice_date' => $oldestDate?->toDateString(),
                'days_outstanding' => $oldestDate ? (int) $oldestDate->diffInDays($asOf, false) : 0,
                'total_balance' => round($totalBalance, 2),
                'buckets'       => array_map(static fn ($v) => round($v, 2), $buckets),
            ];
        })
            ->sortBy([
                ['class_name', 'asc'],
                ['student_name', 'asc'],
            ])
            ->values()
            ->all();

        $totals = $this->emptyBuckets();
        $totalBalance = 0.0;

        foreach ($rows as $row) {
            $totalBalance += $row['total_balance'];
            foreach (array_keys(self::BUCKET_LABELS) as $key) {
                $totals[$key] += $row['buckets'][$key];
            }
        }

        $totals = array_map(static fn ($v) => round($v, 2), $totals);
        $totals['total_balance'] = round($totalBalance, 2);

        return [
            'rows'         => $rows,
            'totals'       => $totals,
            'asOf'         => $asOf,
            'debtorCount'  => count($rows),
        ];
    }

    private function bucketForDays(int $days): string
    {
        if ($days <= 30) {
            return 'current';
        }
        if ($days <= 60) {
            return 'days_31_60';
        }
        if ($days <= 90) {
            return 'days_61_90';
        }

        return 'days_91_plus';
    }

    /** @return array<string, float> */
    private function emptyBuckets(): array
    {
        return [
            'current'      => 0.0,
            'days_31_60'   => 0.0,
            'days_61_90'   => 0.0,
            'days_91_plus' => 0.0,
        ];
    }
}
