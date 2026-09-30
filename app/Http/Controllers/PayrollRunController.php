<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Services\PayrollRunService;
use App\Support\TenantRules;
use Illuminate\Http\Request;
use RuntimeException;

class PayrollRunController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', PayrollRun::class);

        $runs = PayrollRun::orderByDesc('period')->paginate(12);

        return view('payroll.runs.index', compact('runs'));
    }

    public function store(Request $request, PayrollRunService $runs)
    {
        $this->authorize('create', PayrollRun::class);

        $data = $request->validate([
            'period' => ['required', 'date'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        try {
            $run = $runs->open(TenantRules::schoolId(), $data['period']);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('payroll.runs.show', $run->id)
            ->with('success', 'Payroll month ready.');
    }

    public function show($id, PayrollRunService $runs)
    {
        $run = PayrollRun::forSchool()->with(['payslips.lines'])->findOrFail($id);
        $this->authorize('view', $run);

        $payslips = $run->payslips->sortBy('full_name')->values();
        $totals = $runs->totals($run);

        return view('payroll.runs.show', compact('run', 'payslips', 'totals'));
    }

    public function updatePayslip(Request $request, $runId, $payslipId, PayrollRunService $runs)
    {
        $run = PayrollRun::forSchool()->findOrFail($runId);
        $this->authorize('update', $run);

        $payslip = Payslip::forSchool()
            ->where('payroll_run_id', $run->id)
            ->findOrFail($payslipId);

        $data = $request->validate([
            'included' => ['nullable', 'boolean'],
            'adjustment' => ['nullable', 'numeric'],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        try {
            $runs->updatePayslip(
                $payslip,
                $request->boolean('included'),
                (float) ($data['adjustment'] ?? 0)
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payslip updated.');
    }

    public function recalculate($id, PayrollRunService $runs)
    {
        $run = PayrollRun::forSchool()->findOrFail($id);
        $this->authorize('update', $run);

        try {
            $runs->recalculate($run);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payroll month recalculated.');
    }

    public function lock($id, PayrollRunService $runs)
    {
        $run = PayrollRun::forSchool()->findOrFail($id);
        $this->authorize('update', $run);

        try {
            $runs->lock($run, (int) auth()->id());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payroll month locked.');
    }
}
