<?php

namespace App\Http\Controllers;

use App\Models\ClassFee;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Term;
use App\Services\BulkImportExportService;
use App\Support\TenantFilters;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BulkImportExportController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = TenantFilters::schoolId();
        $terms = Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->get();
        $isAdmin = auth()->user()->role === 'admin';

        return view('bulk.index', compact('terms', 'isAdmin'));
    }

    public function export(Request $request, string $type, BulkImportExportService $service)
    {
        $this->authorizeType($type, 'export');

        $schoolId = TenantFilters::validate($request, ['term_id']);
        $termId = $request->filled('term_id') ? (int) $request->term_id : null;

        return match ($type) {
            'students' => $service->exportStudents($schoolId),
            'fees'     => $service->exportFees($schoolId, $termId),
            'payments' => $service->exportPayments($schoolId, $termId),
            'opening_balances' => $service->exportOpeningBalances($schoolId, $termId),
            'employees' => $service->exportEmployees($schoolId),
            default    => abort(404),
        };
    }

    public function template(string $type, BulkImportExportService $service)
    {
        $this->authorizeType($type, 'import');

        try {
            return $service->templateDownload($type);
        } catch (InvalidArgumentException) {
            abort(404);
        }
    }

    public function import(Request $request, string $type, BulkImportExportService $service)
    {
        $this->authorizeType($type, 'import');

        $schoolId = TenantFilters::schoolId();

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('file')->getRealPath();

        $result = match ($type) {
            'students' => $service->importStudents($schoolId, $path),
            'fees'     => $service->importClassFees($schoolId, $path),
            'payments' => $service->importPayments($schoolId, $path),
            'opening_balances' => $service->importOpeningBalances($schoolId, $path),
            'employees' => $service->importEmployees($schoolId, $path),
            default    => abort(404),
        };

        $message = sprintf(
            'Import finished: %d created, %d updated, %d skipped.',
            $result['created'],
            $result['updated'],
            $result['skipped']
        );

        if ($result['errors'] !== []) {
            return back()
                ->with('warning', $message)
                ->with('import_errors', array_slice($result['errors'], 0, 20));
        }

        return back()->with('success', $message);
    }

    private function authorizeType(string $type, string $action): void
    {
        if (! in_array($type, ['students', 'fees', 'payments', 'opening_balances', 'employees'], true)) {
            abort(404);
        }

        if ($type === 'students') {
            $action === 'export'
                ? $this->authorize('viewAny', Student::class)
                : $this->authorize('create', Student::class);

            return;
        }

        if ($type === 'fees' && $action === 'import') {
            $this->authorize('create', ClassFee::class);

            return;
        }

        if ($type === 'payments' && $action === 'import') {
            abort_unless(in_array(auth()->user()->role, ['admin', 'accountant'], true), 403);

            return;
        }

        if ($type === 'opening_balances') {
            abort_unless(in_array(auth()->user()->role, ['admin', 'accountant'], true), 403);

            return;
        }

        if ($type === 'employees') {
            $action === 'export'
                ? $this->authorize('viewAny', Employee::class)
                : $this->authorize('create', Employee::class);

            return;
        }

        abort_unless(in_array(auth()->user()->role, ['admin', 'accountant'], true), 403);
    }
}
