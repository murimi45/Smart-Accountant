<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtraFee;
use App\Models\Student;
use App\Models\Term;
use App\Models\ClassFee;
use App\Models\Classes;
use App\Models\StudentExtraFee;
use App\Models\StudentEnrollment;
use App\Services\InvoiceService;
use App\Support\TenantRules;

class ExtraFeeController extends Controller
{
    public function listExtraFee(){

        $this->authorize('viewAny', ExtraFee::class);

        $extraFees= ExtraFee::with('creator')->get();
        return view('extrafee.list', compact('extraFees'));
        
    }

     public function addExtraFee()
      {
            $this->authorize('create', ExtraFee::class);

            $data['terms']=Term::all();
            return view('extrafee.add',$data);
     }


     
    public function insertExtraFee(Request $request){
        $this->authorize('create', ExtraFee::class);

        $schoolId=auth()->user()->school_id;
        $userId=auth()->id();

        $validated= $request->validate([

            'name' => 'required|string|max:255',
            'amount'=>'nullable|numeric',
            'is_quantity_based' => 'required|boolean',
             'description'=>'required|string',
             'term_id'=>['required', TenantRules::terms()],
            'status'=>'required|string|in:active,inactive',
            'school_id' => TenantRules::prohibitedSchoolId(),
            
        ]);
        $term = Term::forSchool($schoolId)->findOrFail($validated['term_id']);
        $validated['year']=$term->year;
        $validated['created_by']=$userId;
        unset($validated['school_id']);

        ExtraFee::create($validated);

        return redirect()->route('extrafeelist')->with('success','ExtraFee added Successfully');

    }

     public function updateExtraFee($id)
        {
            
           $extrafee = ExtraFee::forSchool()->findOrFail($id);
           $this->authorize('update', $extrafee);
           
           $terms = Term::with('academicYear')->orderByDesc('start_date')->get();
           return view('extrafee.edit', compact('extrafee','terms'));

        }

    public function editExtraFee(Request $request,$id){

        $extrafee = ExtraFee::forSchool()->findOrFail($id);
        $this->authorize('update', $extrafee);
        $schoolId = auth()->user()->school_id;

        $validated= $request->validate([
            'name' => 'required|string|max:255',
            'amount'=>'nullable|numeric',
            'is_quantity_based' => 'required|boolean',
            'description'=>'required|string',
            'status'=>'required|string|in:active,inactive',
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        unset($validated['school_id']);

        $extrafee->update($validated);


        return redirect()->route('extrafeelist')->with('success','ExtraFee updated Successfully');

        }

         public function deleteExtraFee($id)
      {
              $extraFee = ExtraFee::forSchool()->findOrFail($id);
              $this->authorize('delete', $extraFee);
              $extraFee->delete();
              return redirect()->back()->with('success', 'Extra Fee deleted successfully.');
    }




//These methods are used to assign the extra fee to particular students

public function assignStudentExtraFee(Request $request)
{
    $request->validate([
        'extra_fee_id'            => ['required', TenantRules::extraFees()],
        'students'                => 'required|array',
        'students.*.student_id'   => ['required', TenantRules::students()],
        'students.*.quantity'     => 'nullable|numeric|min:1',
        'school_id'               => TenantRules::prohibitedSchoolId(),
    ]);

    $extraFee = ExtraFee::forSchool()->findOrFail($request->extra_fee_id);
    $this->authorize('update', $extraFee);
    $schoolId = auth()->user()->school_id;

    // collect student fee records for bulk insert/update
    $studentFees = [];
    $updatedStudentIds = [];

    foreach ($request->students as $studentId => $studentData) {
        if (empty($studentData['selected'])) {
            continue;
        }

        $student = Student::forSchool($schoolId)->find($studentData['student_id']);
        if (! $student) {
            continue;
        }

        $quantity = !empty($studentData['quantity']) ? (int) $studentData['quantity'] : 1;
        $amount   = $extraFee->amount;
        $total    = $quantity * $amount;

        // Instead of firing observer per student, we collect
        $studentFees[] = [
            'student_id'     => $student->id,
            'extra_fee_id'   => $extraFee->id,
            'quantity'       => $quantity,
            'amount'         => $total,
            'school_id'      => auth()->user()->school_id,
            'created_by'     => auth()->user()->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        $updatedStudentIds[] = $student->id;
    }

    if (!empty($studentFees)) {
        // 🛑 prevent observer from running during batch
        app()->instance('batchAssigningExtraFees', true);

        // use upsert so existing records update instead of duplicate
        StudentExtraFee::upsert(
            $studentFees,
            ['student_id', 'extra_fee_id', 'school_id'],
            ['quantity', 'amount', 'created_by', 'updated_at']
        );

        // ✅ Refresh to make sure term_id is available
        $extraFee->refresh();

        // 🔑 Fire invoice updates once per student
        $students = Student::whereIn('id', $updatedStudentIds)->get();
        foreach ($students as $student) {
            app(InvoiceService::class)->createOrUpdateInvoice($schoolId, $student, $extraFee->term_id);
        }

        // Reactivate observer
        app()->forgetInstance('batchAssigningExtraFees');
    }

    return redirect()
        ->route('listextrafeestudents')
        ->with('success', 'Extra Fee(s) assigned successfully.');
}


  public function showAssignExtraFeeForm(Request $request)
{
    $schoolId = auth()->user()->school_id;
    $activeTerm = Term::current1($schoolId);
    $extraFees = ExtraFee::all();
    $classes = Classes::all();

    // Start with empty students (until an extra fee is chosen)
    $students = collect();

    // Load students only if an extra fee is selected
    if ($request->filled('extra_fee_id')) {
        $studentsQuery = Student::where('school_id', $schoolId)
            ->whereHas('enrollments', function ($q) use ($request, $activeTerm) {
                $q->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED]);

                if ($activeTerm) {
                    $q->where('term_id', $activeTerm->id);
                }

                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->class_id);
                }
            })
            ->with(['enrollments' => function ($q) use ($activeTerm) {
                $q->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED]);

                if ($activeTerm) {
                    $q->where('term_id', $activeTerm->id);
                }

                $q->with('schoolClass')->latest();
            }]);

        if ($request->filled('search')) {
            $studentsQuery->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission', 'like', '%' . $request->search . '%');
            });
        }

        $students = $studentsQuery->orderBy('full_name')->get();
    }

    // Get assigned fees only if extra fee is selected
    $assignedExtraFees = collect();
    if ($request->filled('extra_fee_id')) {
        $assignedExtraFees = StudentExtraFee::where('extra_fee_id', $request->extra_fee_id)
            ->where('school_id', auth()->user()->school_id)
            ->get()
            ->keyBy('student_id');
    }

    return view('extrafee.assignextrafee', compact(
        'extraFees', 'students', 'assignedExtraFees', 'classes'
    ))->with([
        'selectedExtraFee' => $request->extra_fee_id,
        'selectedClass'    => $request->class_id,
        'searchQuery'      => $request->search
    ]);
}




    public function listExtraFeeStudent(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = StudentExtraFee::where('school_id', $schoolId);

        if($request->filled('extra_fee_id')){
            $query->where('extra_fee_id',$request->extra_fee_id);
        }

        if($request->filled('student_name')){
            $search = $request->student_name;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', '%'.$search.'%')
                  ->orWhere('admission', 'like', '%'.$search.'%');
            });
        }

        $extraFeeStudents=$query->with(['extraFee.term','student','creator'])->get();
        $extraFees=ExtraFee::all();

        return view('extrafee.extrafeestudent', compact('extraFeeStudents','extraFees'));

    }


     public function editAssignedExtraFee($id)
    {
        $schoolId = auth()->user()->school_id;
        $assignedFee = StudentExtraFee::with('extraFee')->where('school_id', $schoolId)->findOrFail($id);
        $this->authorize('update', $assignedFee);
        $termId = $assignedFee->extraFee?->term_id ?? Term::current1($schoolId)?->id;

        $students = Student::where('school_id', $schoolId)
            ->with(['enrollments' => function ($q) use ($termId) {
                $q->whereNotIn('status', [StudentEnrollment::STATUS_CANCELLED]);

                if ($termId) {
                    $q->where('term_id', $termId);
                }

                $q->with('schoolClass')->latest();
            }])
            ->orderBy('full_name')
            ->get();

        $assignedStudents = StudentExtraFee::where('school_id', $schoolId)
            ->where('extra_fee_id', $assignedFee->extra_fee_id)
            ->get();
        $extraFees = ExtraFee::with('term')->get();

        return view('extrafee.editextrafeestudent', compact(
            'assignedFee',
            'extraFees',
            'students',
            'assignedStudents'
        ));
         
    }


    public function updateAssignedExtraFee(Request $request, $id)
    {
        $schoolId = auth()->user()->school_id;
        $assignedFee = StudentExtraFee::where('school_id', $schoolId)->findOrFail($id);
        $this->authorize('update', $assignedFee);
        $originalExtraFeeId = (int) $assignedFee->extra_fee_id;

        $request->validate([
            'extra_fee_id'            => ['required', TenantRules::extraFees()],
            'students'                => 'required|array',
            'students.*.student_id'   => ['required', TenantRules::students()],
            'students.*.quantity'       => 'nullable|numeric|min:1',
            'school_id'               => TenantRules::prohibitedSchoolId(),
        ]);

        $extraFee = ExtraFee::forSchool()->findOrFail($request->extra_fee_id);
        $newExtraFeeId = (int) $extraFee->id;

        $selected = [];
        foreach ($request->students as $studentData) {
            if (empty($studentData['selected'])) {
                continue;
            }

            $student = Student::where('school_id', $schoolId)->find($studentData['student_id'] ?? null);
            if (! $student) {
                continue;
            }

            $quantity = ! empty($studentData['quantity']) ? (int) $studentData['quantity'] : 1;
            $selected[$student->id] = $quantity;
        }

        $affectedStudentIds = [];
        app()->instance('batchAssigningExtraFees', true);

        try {
            if ($originalExtraFeeId !== $newExtraFeeId) {
                $removed = StudentExtraFee::where('school_id', $schoolId)
                    ->where('extra_fee_id', $originalExtraFeeId)
                    ->get();

                foreach ($removed as $assignment) {
                    $affectedStudentIds[] = $assignment->student_id;
                    $assignment->delete();
                }
            } else {
                $toRemove = StudentExtraFee::where('school_id', $schoolId)
                    ->where('extra_fee_id', $originalExtraFeeId)
                    ->whereNotIn('student_id', array_keys($selected))
                    ->get();

                foreach ($toRemove as $assignment) {
                    $affectedStudentIds[] = $assignment->student_id;
                    $assignment->delete();
                }
            }

            $studentFees = [];
            foreach ($selected as $studentId => $quantity) {
                $total = $quantity * $extraFee->amount;

                $studentFees[] = [
                    'student_id'   => $studentId,
                    'extra_fee_id' => $newExtraFeeId,
                    'quantity'     => $quantity,
                    'amount'       => $total,
                    'school_id'    => $schoolId,
                    'created_by'   => auth()->id(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                $affectedStudentIds[] = $studentId;
            }

            if ($studentFees !== []) {
                StudentExtraFee::upsert(
                    $studentFees,
                    ['student_id', 'extra_fee_id', 'school_id'],
                    ['quantity', 'amount', 'created_by', 'updated_at']
                );
            }

            $extraFee->refresh();
            $affectedStudentIds = array_unique($affectedStudentIds);

            foreach ($affectedStudentIds as $studentId) {
                $student = Student::forSchool($schoolId)->find($studentId);
                if (! $student) {
                    continue;
                }

                app(InvoiceService::class)->createOrUpdateInvoice($schoolId, $student, $extraFee->term_id);

                if ($originalExtraFeeId !== $newExtraFeeId) {
                    $originalFee = ExtraFee::forSchool($schoolId)->find($originalExtraFeeId);
                    if ($originalFee?->term_id) {
                        app(InvoiceService::class)->createOrUpdateInvoice($schoolId, $student, $originalFee->term_id);
                    }
                }
            }
        } finally {
            app()->forgetInstance('batchAssigningExtraFees');
        }

        return redirect()
            ->route('listextrafeestudents')
            ->with('success', 'Assigned extra fee updated successfully.');
    }



   public function deleteAssignedExtraFee($id)
{
    $assignedFee = StudentExtraFee::with('extraFee', 'student')
        ->where('school_id', auth()->user()->school_id)
        ->findOrFail($id);
    $this->authorize('delete', $assignedFee);

    $student = $assignedFee->student;
    $termId  = $assignedFee->extraFee?->term_id;

    $assignedFee->delete();

    if ($student && $termId) {
        app(\App\Services\InvoiceService::class)->createOrUpdateInvoice(
            (int) auth()->user()->school_id,
            $student,
            $termId
        );
    }

    return redirect()->back()->with('success', 'Extra Fee deleted successfully.');
}
    
    }
