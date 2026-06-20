<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\ClassFee;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Support\CsvStream;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkImportExportService
{
    /** @return array<string, list<string>> */
    public function templates(): array
    {
        return [
            'students' => [
                'admission', 'full_name', 'phone', 'guardian_name', 'gender', 'class', 'term', 'stream',
            ],
            'fees' => [
                'class', 'term', 'amount', 'description', 'status',
            ],
            'payments' => [
                'admission', 'amount', 'method', 'payment_date', 'term',
            ],
            'opening_balances' => [
                'admission', 'term', 'opening_balance', 'notes',
            ],
        ];
    }

    public function exportStudents(int $schoolId): StreamedResponse
    {
        $rows = Student::where('school_id', $schoolId)
            ->with(['enrollments.schoolClass', 'enrollments.stream', 'enrollments.term.academicYear'])
            ->orderBy('full_name')
            ->get()
            ->flatMap(function (Student $student) {
                $enrollments = $student->enrollments->sortByDesc('id');

                if ($enrollments->isEmpty()) {
                    return [[
                        $student->admission,
                        $student->full_name,
                        $student->phone,
                        $student->guardian_name,
                        $student->gender,
                        '', '', '', '',
                    ]];
                }

                return $enrollments->map(fn (StudentEnrollment $e) => [
                    $student->admission,
                    $student->full_name,
                    $student->phone,
                    $student->guardian_name,
                    $student->gender,
                    $e->schoolClass?->name ?? '',
                    $this->termLabel($e->term),
                    $e->stream?->name ?? '',
                    $e->status,
                ]);
            });

        return CsvStream::download(
            'students-'.now()->format('Y-m-d').'.csv',
            ['admission', 'full_name', 'phone', 'guardian_name', 'gender', 'class', 'term', 'stream', 'enrollment_status'],
            $rows
        );
    }

    public function exportFees(int $schoolId, ?int $termId = null): StreamedResponse
    {
        $query = Invoice::where('school_id', $schoolId)
            ->excludeVoided()
            ->with(['student', 'term.academicYear', 'enrollment.schoolClass']);

        if ($termId) {
            $query->where('term_id', $termId);
        }

        $rows = $query->orderBy('invoice_date')->get()->map(fn (Invoice $invoice) => [
            $invoice->student?->admission ?? '',
            $invoice->student?->full_name ?? '',
            $invoice->enrollment?->schoolClass?->name ?? '',
            $this->termLabel($invoice->term),
            number_format((float) $invoice->total_amount, 2, '.', ''),
            number_format((float) $invoice->amount_paid, 2, '.', ''),
            number_format((float) $invoice->balance, 2, '.', ''),
            $invoice->status,
            $invoice->invoice_date ? (\Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('Y-m-d')) : '',
        ]);

        return CsvStream::download(
            'fees-'.now()->format('Y-m-d').'.csv',
            ['admission', 'full_name', 'class', 'term', 'total_amount', 'amount_paid', 'balance', 'status', 'invoice_date'],
            $rows
        );
    }

    public function exportPayments(int $schoolId, ?int $termId = null): StreamedResponse
    {
        $query = InvoicePayment::query()
            ->whereHas('invoice', fn ($q) => $q->where('school_id', $schoolId)->excludeVoided())
            ->with(['invoice.student', 'invoice.term.academicYear']);

        if ($termId) {
            $query->whereHas('invoice', fn ($q) => $q->where('term_id', $termId));
        }

        $rows = $query->orderByDesc('payment_date')->get()->map(fn (InvoicePayment $payment) => [
            $payment->invoice?->student?->admission ?? '',
            $payment->invoice?->student?->full_name ?? '',
            $this->termLabel($payment->invoice?->term),
            number_format((float) $payment->amount, 2, '.', ''),
            $payment->method,
            $payment->payment_date,
            $payment->invoice_id,
            $payment->id,
        ]);

        return CsvStream::download(
            'payments-'.now()->format('Y-m-d').'.csv',
            ['admission', 'full_name', 'term', 'amount', 'method', 'payment_date', 'invoice_id', 'payment_id'],
            $rows
        );
    }

    public function templateDownload(string $type): StreamedResponse
    {
        $templates = $this->templates();

        if (! isset($templates[$type])) {
            throw new InvalidArgumentException('Unknown template type.');
        }

        $headers = $templates[$type];
        $sample = match ($type) {
            'students' => ['ADM001', 'Jane Doe', '0712345678', 'John Doe', 'female', 'Grade 1', 'Term 1 - 2026', 'A'],
            'fees'     => ['Grade 1', 'Term 1 - 2026', '15000', 'Tuition Term 1', 'active'],
            'payments' => ['ADM001', '5000', 'Mpesa', now()->toDateString(), 'Term 1 - 2026'],
            'opening_balances' => ['ADM001', 'Term 1 - 2026', '3500', 'Arrears 2025 Term 3'],
            default    => array_fill(0, count($headers), ''),
        };

        return CsvStream::download("template-{$type}.csv", $headers, [$sample]);
    }

    /** @return array{created: int, updated: int, skipped: int, errors: list<string>} */
    public function importStudents(int $schoolId, string $filePath): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $rows = CsvStream::readUploaded($filePath);

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            try {
                $admission = $row['admission'] ?? '';
                $fullName = $row['full_name'] ?? '';
                $className = $row['class'] ?? '';
                $termLabel = $row['term'] ?? '';

                if ($admission === '' || $fullName === '' || $className === '' || $termLabel === '') {
                    throw new InvalidArgumentException('admission, full_name, class, and term are required.');
                }

                $gender = strtolower($row['gender'] ?? 'male');
                if (! in_array($gender, ['male', 'female'], true)) {
                    throw new InvalidArgumentException('gender must be male or female.');
                }

                $class = $this->resolveClass($schoolId, $className);
                $term = $this->resolveTerm($schoolId, $termLabel);

                if (! $class || ! $term) {
                    throw new InvalidArgumentException('Could not find class or term for this school.');
                }

                $streamId = null;
                if (! empty($row['stream'])) {
                    $stream = Stream::where('class_id', $class->id)
                        ->where('name', $row['stream'])
                        ->first();
                    $streamId = $stream?->id;
                }

                $student = Student::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('admission', $admission)
                    ->first();

                if ($student) {
                    $student->update([
                        'full_name'     => $fullName,
                        'phone'         => $row['phone'] ?? null,
                        'guardian_name' => $row['guardian_name'] ?? null,
                        'gender'        => $gender,
                    ]);
                    $result['updated']++;
                } else {
                    $student = Student::createForSchool($schoolId, [
                        'admission'     => $admission,
                        'full_name'     => $fullName,
                        'phone'         => $row['phone'] ?? null,
                        'guardian_name' => $row['guardian_name'] ?? null,
                        'gender'        => $gender,
                    ]);
                    $result['created']++;
                }

                $enrollmentExists = StudentEnrollment::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED])
                    ->exists();

                if (! $enrollmentExists) {
                    StudentEnrollment::createForSchool($schoolId, [
                        'student_id' => $student->id,
                        'class_id'   => $class->id,
                        'stream_id'  => $streamId,
                        'term_id'    => $term->id,
                        'status'     => StudentEnrollment::STATUS_ACTIVE,
                    ]);
                }
            } catch (\Throwable $e) {
                $result['errors'][] = "Row {$line}: ".$e->getMessage();
            }
        }

        return $result;
    }

    /** @return array{created: int, updated: int, skipped: int, errors: list<string>} */
    public function importClassFees(int $schoolId, string $filePath): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $rows = CsvStream::readUploaded($filePath);

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            try {
                $className = $row['class'] ?? '';
                $termLabel = $row['term'] ?? '';
                $amount = (float) ($row['amount'] ?? 0);

                if ($className === '' || $termLabel === '' || $amount <= 0) {
                    throw new InvalidArgumentException('class, term, and amount are required.');
                }

                $class = $this->resolveClass($schoolId, $className);
                $term = $this->resolveTerm($schoolId, $termLabel);

                if (! $class || ! $term || ! $term->year) {
                    throw new InvalidArgumentException('Could not find class/term or term has no academic year.');
                }

                $status = strtolower($row['status'] ?? 'active');
                if (! in_array($status, ['active', 'inactive'], true)) {
                    $status = 'active';
                }

                $fee = ClassFee::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('class_id', $class->id)
                    ->where('term_id', $term->id)
                    ->first();

                $payload = [
                    'amount'      => $amount,
                    'description' => $row['description'] ?? ('Class fee '.$class->name),
                    'status'      => $status,
                    'year'        => $term->year,
                ];

                if ($fee) {
                    $fee->update($payload);
                    $result['updated']++;
                } else {
                    ClassFee::withoutGlobalScopes()->create(array_merge($payload, [
                        'school_id' => $schoolId,
                        'class_id'  => $class->id,
                        'term_id'   => $term->id,
                    ]));
                    $result['created']++;
                }
            } catch (\Throwable $e) {
                $result['errors'][] = "Row {$line}: ".$e->getMessage();
            }
        }

        return $result;
    }

    /** @return array{created: int, updated: int, skipped: int, errors: list<string>} */
    public function importPayments(int $schoolId, string $filePath): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $rows = CsvStream::readUploaded($filePath);
        $invoiceService = app(InvoiceService::class);
        $defaultTerm = InvoiceService::currentTermForSchool($schoolId);

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            try {
                $admission = $row['admission'] ?? '';
                $amount = (float) ($row['amount'] ?? 0);
                $method = $row['method'] ?? 'Cash';
                $paymentDate = $row['payment_date'] ?? now()->toDateString();

                if ($admission === '' || $amount <= 0) {
                    throw new InvalidArgumentException('admission and amount are required.');
                }

                $student = Student::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('admission', $admission)
                    ->first();

                if (! $student) {
                    throw new InvalidArgumentException("Student admission {$admission} not found.");
                }

                $term = ! empty($row['term'])
                    ? $this->resolveTerm($schoolId, $row['term'])
                    : $defaultTerm;

                if (! $term) {
                    throw new InvalidArgumentException('Term not found. Add a term column or set a current term.');
                }

                $invoice = Invoice::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->excludeVoided()
                    ->first();

                if (! $invoice) {
                    throw new InvalidArgumentException("No invoice for {$admission} in {$this->termLabel($term)}.");
                }

                $invoiceService->paymentMade($invoice, $amount, $method);

                InvoicePayment::where('invoice_id', $invoice->id)
                    ->latest('id')
                    ->limit(1)
                    ->update(['payment_date' => $paymentDate]);

                $result['created']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Row {$line}: ".$e->getMessage();
            }
        }

        return $result;
    }

    public function exportOpeningBalances(int $schoolId, ?int $termId = null): StreamedResponse
    {
        $query = Invoice::where('school_id', $schoolId)
            ->excludeVoided()
            ->where('imported_opening_balance', '>', 0)
            ->with(['student', 'term.academicYear']);

        if ($termId) {
            $query->where('term_id', $termId);
        }

        $rows = $query->get()->map(fn (Invoice $invoice) => [
            $invoice->student?->admission ?? '',
            $invoice->student?->full_name ?? '',
            $this->termLabel($invoice->term),
            number_format((float) $invoice->imported_opening_balance, 2, '.', ''),
            $invoice->opening_balance_notes ?? '',
            number_format((float) $invoice->total_amount, 2, '.', ''),
            number_format((float) $invoice->balance, 2, '.', ''),
        ]);

        return CsvStream::download(
            'opening-balances-'.now()->format('Y-m-d').'.csv',
            ['admission', 'full_name', 'term', 'opening_balance', 'notes', 'invoice_total', 'balance'],
            $rows
        );
    }

    /** @return array{created: int, updated: int, skipped: int, errors: list<string>} */
    public function importOpeningBalances(int $schoolId, string $filePath): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $rows = CsvStream::readUploaded($filePath);
        $invoiceService = app(InvoiceService::class);
        $openingService = app(OpeningBalanceService::class);

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            try {
                $admission = $row['admission'] ?? '';
                $termLabel = $row['term'] ?? '';
                $amount = (float) ($row['opening_balance'] ?? 0);
                $notes = $row['notes'] ?? null;

                if ($admission === '' || $termLabel === '' || $amount <= 0) {
                    throw new InvalidArgumentException('admission, term, and opening_balance are required.');
                }

                $student = Student::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('admission', $admission)
                    ->first();

                if (! $student) {
                    throw new InvalidArgumentException("Student {$admission} not found.");
                }

                $term = $this->resolveTerm($schoolId, $termLabel);

                if (! $term) {
                    throw new InvalidArgumentException("Term {$termLabel} not found.");
                }

                $enrollment = StudentEnrollment::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED])
                    ->first();

                if (! $enrollment) {
                    throw new InvalidArgumentException("No enrollment for {$admission} in {$termLabel}. Enroll the student first.");
                }

                $invoice = Invoice::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('enrollment_id', $enrollment->id)
                    ->excludeVoided()
                    ->first();

                if (! $invoice) {
                    $invoice = $invoiceService->createOrUpdateInvoice(
                        $schoolId,
                        $student,
                        $term->id,
                        $enrollment->id
                    );
                }

                if (! $invoice) {
                    throw new InvalidArgumentException('Could not create invoice for this enrollment.');
                }

                $hadBalance = (float) $invoice->imported_opening_balance > 0;

                $openingService->apply($invoice, $amount, $notes);

                $hadBalance ? $result['updated']++ : $result['created']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Row {$line}: ".$e->getMessage();
            }
        }

        return $result;
    }

    private function termLabel(?Term $term): string
    {
        if (! $term) {
            return '';
        }

        $year = $term->year ?? $term->academicYear?->name ?? '';

        return trim($term->name.($year ? ' - '.$year : ''));
    }

    private function resolveClass(int $schoolId, string $name): ?Classes
    {
        return Classes::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('name', trim($name))
            ->first();
    }

    private function resolveTerm(int $schoolId, string $label): ?Term
    {
        $label = trim($label);

        return Term::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('academicYear')
            ->get()
            ->first(function (Term $term) use ($label) {
                return strcasecmp($this->termLabel($term), $label) === 0
                    || strcasecmp($term->name, $label) === 0;
            });
    }
}
