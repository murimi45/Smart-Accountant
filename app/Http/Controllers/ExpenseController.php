<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Term;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ExpenseRecordedNotification;

class ExpenseController extends Controller
{
   public function index(Request $request)
{
    $schoolId = auth()->user()->school_id;

    // Start with a query builder — DO NOT call get()/paginate() yet
    $query = Expense::with('category')
                    ->where('school_id', $schoolId);

    // Apply filters
    if ($request->filled('category_id')) {
        $query->where('expense_category_id', $request->category_id);
    }

    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    if ($request->filled('description')) {
        $query->where('description', 'LIKE', '%' . $request->description . '%');
    }

    $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();

    $categories = ExpenseCategory::all();

    return view('expensesdefined.index', compact('expenses','categories'));
}


     public function create()
     {
         $categories = ExpenseCategory::all();
         $expense = null; 
         $terms=Term::where('school_id', auth()->user()->school_id)->get();
         return view('expensesdefined.create', compact('categories', 'expense','terms'));
     }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        $terms=Term::where('school_id', auth()->user()->school_id)->get();
        return view('expensesdefined.create', compact('categories', 'expense','terms'));
    }


   public function store(Request $request)
{
    $data = $request->validate([
        'expense_category_id' => 'nullable|exists:expense_categories,id',
        'description' => 'nullable|string',
        'amount' => 'required|numeric|min:0.01',
        'payment_method' => 'nullable|string',
        'expense_date' => 'nullable|date',
        'term_id' => 'required|exists:terms,id',
    ]);

    $term = Term::findOrFail($data['term_id']);
    $data['year'] = $term->year;
    $data['school_id'] = auth()->user()->school_id;
    $data['created_by'] = auth()->id();

    // ✅ Create expense (your original logic)
    $expense = Expense::create($data);

    // ✅ Send dashboard notification (new logic)
    $user = Auth::user();
    $user->notify(new ExpenseRecordedNotification([
        'title' => 'Expense Recorded',
        'message' => 'An expense of KES ' 
                        . number_format($expense->amount) 
                        . ' for ' 
                        . ($expense->category->name ?? 'General Expense') 
                        . ' has been recorded.',
    ]));

    return redirect()
        ->route('expenses.index')
        ->with('success', 'Expense recorded.');
}


    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);
         
        $data = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'expense_date' => 'nullable|date',
            'term_id'=>'required|exists:terms,id',
            'year'=>'required|digits:4|integer',
        ]);

        $expense->update($data); // observer updates the original cashbook entry
        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
         $this->authorize('delete', $expense);
        $expense->delete(); // soft delete -> observer creates reversal
        return redirect()->back()->with('success', 'Expense deleted (reversed in cashbook).');
    }

    public function restore($id)
    {
        $expense = Expense::withTrashed()->findOrFail($id);
        $this->authorize('restore', $expense);
        $expense->restore(); // observer creates "restored" cashbook entry
        return redirect()->back()->with('success', 'Expense restored.');
    }
}
