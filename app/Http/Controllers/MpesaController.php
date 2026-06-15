<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Term;
use App\Support\MpesaTenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\InvoiceService;
use InvalidArgumentException;

class MpesaController extends Controller
{
    public function validatePayment(Request $request)
    {
        Log::info('M-Pesa Validation Request:', $request->all());

        $channel = MpesaTenantResolver::resolveChannel($request);

        if (! $channel) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Unknown or inactive paybill/till number',
            ]);
        }

        $admission = MpesaTenantResolver::extractAdmission($request);

        if (! $admission) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Missing admission number',
            ]);
        }

        $student = MpesaTenantResolver::findStudentInSchool((int) $channel->school_id, $admission);

        if (! $student) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Invalid admission number for this school',
            ]);
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function confirmPayment(Request $request, InvoiceService $invoiceService)
    {
        Log::info('M-Pesa Confirmation Request:', ['body' => $request->getContent()]);

        $mpesaTransId = $request->input('TransID');
        $amount       = $request->input('TransAmount');
        $phone        = $request->input('MSISDN');

        if (Transaction::withoutGlobalScopes()->where('reference', $mpesaTransId)->exists()) {
            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Duplicate transaction',
            ]);
        }

        $channel = MpesaTenantResolver::resolveChannel($request);

        if (! $channel) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'No school found for this paybill/till',
            ]);
        }

        $schoolId  = (int) $channel->school_id;
        $admission = MpesaTenantResolver::extractAdmission($request);

        if (! $admission) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Missing admission number',
            ]);
        }

        $student = MpesaTenantResolver::findStudentInSchool($schoolId, $admission);

        if (! $student) {
            Transaction::createForSchool($schoolId, [
                'amount'      => $amount,
                'reference'   => $mpesaTransId,
                'phone'       => $phone,
                'status'      => 'rejected',
                'raw_payload' => $request->all(),
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Admission number not found in this school',
            ]);
        }

        $currentTerm = Term::current1($schoolId);

        if (! $currentTerm) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'No active term found for the school',
            ]);
        }

        $invoice = Invoice::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('term_id', $currentTerm->id)
            ->collectible()
            ->first();

        if (! $invoice) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'No payable invoice found for student in current term',
            ]);
        }

        try {
            $invoiceService->paymentMade($invoice, (float) $amount, 'mpesa');
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => $e->getMessage(),
            ]);
        }

        $invoice->refresh();

        Transaction::createForSchool($schoolId, [
            'student_id'  => $student->id,
            'invoice_id'  => $invoice->id,
            'amount'      => $amount,
            'reference'   => $mpesaTransId,
            'phone'       => $phone,
            'status'      => $invoice->balance < 0 ? 'overpaid' : 'applied',
            'raw_payload' => $request->all(),
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Payment processed successfully',
        ]);
    }
}
