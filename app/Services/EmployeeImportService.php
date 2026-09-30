<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryGrade;
use App\Support\CsvStream;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeImportService
{
    /** @return list<string> */
    public function headers(): array
    {
        return [
            'staff_number',
            'full_name',
            'grade',
            'phone',
            'status',
            'start_date',
            'kra_pin',
            'nssf_number',
            'shif_number',
            'payment_method',
            'bank_name',
            'account_number',
        ];
    }

    public function export(int $schoolId): StreamedResponse
    {
        $rows = Employee::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('grade')
            ->orderBy('full_name')
            ->get()
            ->map(fn (Employee $employee) => [
                $employee->staff_number,
                $employee->full_name,
                $employee->grade?->name ?? '',
                $employee->phone,
                $employee->status,
                $employee->start_date?->format('Y-m-d') ?? '',
                $employee->kra_pin,
                $employee->nssf_number,
                $employee->shif_number,
                $employee->payment_method,
                $employee->bank_name,
                $employee->account_number,
            ]);

        return CsvStream::download(
            'employees-'.now()->format('Y-m-d').'.csv',
            $this->headers(),
            $rows
        );
    }

    /**
     * @return array{created: int, updated: int, skipped: int, errors: list<string>}
     */
    public function importFile(int $schoolId, string $filePath): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach (CsvStream::readUploaded($filePath) as $index => $row) {
            $line = $index + 2;

            try {
                $this->importRow($schoolId, $row, $result);
            } catch (InvalidArgumentException $e) {
                $result['skipped']++;
                $result['errors'][] = "Line {$line}: {$e->getMessage()}";
            }
        }

        return $result;
    }

    /**
     * @param  array<string, string>  $row
     * @param  array{created: int, updated: int, skipped: int, errors: list<string>}  $result
     */
    private function importRow(int $schoolId, array $row, array &$result): void
    {
        $staffNumber = $row['staff_number'] ?? '';
        $fullName = $row['full_name'] ?? '';
        $gradeName = $row['grade'] ?? '';

        if ($staffNumber === '' || $fullName === '' || $gradeName === '') {
            throw new InvalidArgumentException('staff_number, full_name, and grade are required.');
        }

        $grade = SalaryGrade::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('name', $gradeName)
            ->first();

        if (! $grade) {
            throw new InvalidArgumentException("grade \"{$gradeName}\" was not found. Create the grade first.");
        }

        $status = strtolower($row['status'] ?? '') ?: Employee::STATUS_ACTIVE;
        if (! in_array($status, [Employee::STATUS_ACTIVE, Employee::STATUS_LEFT], true)) {
            throw new InvalidArgumentException('status must be active or left.');
        }

        $paymentMethod = strtolower($row['payment_method'] ?? '');
        if ($paymentMethod !== '' && ! in_array($paymentMethod, [Employee::PAY_BANK, Employee::PAY_MPESA, Employee::PAY_CASH], true)) {
            throw new InvalidArgumentException('payment_method must be bank, mpesa, or cash.');
        }

        $attributes = [
            'full_name' => $fullName,
            'phone' => ($row['phone'] ?? '') !== '' ? $row['phone'] : null,
            'status' => $status,
            'start_date' => ($row['start_date'] ?? '') !== '' ? $row['start_date'] : null,
            'kra_pin' => ($row['kra_pin'] ?? '') !== '' ? $row['kra_pin'] : null,
            'nssf_number' => ($row['nssf_number'] ?? '') !== '' ? $row['nssf_number'] : null,
            'shif_number' => ($row['shif_number'] ?? '') !== '' ? $row['shif_number'] : null,
            'payment_method' => $paymentMethod !== '' ? $paymentMethod : null,
            'bank_name' => ($row['bank_name'] ?? '') !== '' ? $row['bank_name'] : null,
            'account_number' => ($row['account_number'] ?? '') !== '' ? $row['account_number'] : null,
            'salary_grade_id' => $grade->id,
        ];

        $employee = Employee::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('staff_number', $staffNumber)
            ->first();

        if ($employee) {
            $employee->update($attributes);
            $result['updated']++;

            return;
        }

        Employee::createForSchool($schoolId, array_merge($attributes, [
            'staff_number' => $staffNumber,
        ]));
        $result['created']++;
    }
}