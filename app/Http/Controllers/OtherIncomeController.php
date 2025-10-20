<?php



namespace App\Http\Controllers;

use App\Models\OtherIncome;
use App\Models\IncomeCategory;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OtherIncomeNotification;


class OtherIncomeController extends Controller
{
    public function index()
    {
        $incomes = OtherIncome::with('category')->where('school_id', auth()->user()->school_id)->latest()->get();
        return view('income.index', compact('incomes'));
    }

    public function create()
    {
        $categories = IncomeCategory::where('school_id', auth()->user()->school_id)->get();
        $terms =Term::where('school_id', auth()->user()->school_id)->get();
        return view('income.create', compact('categories','terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'income_date' => 'required|date',
            'term_id'=>'required|exists:terms,id',
            
        ]);

        $term = Term::findOrFail($request->term_id);

$incomeCategory = IncomeCategory::find($request->income_category_id);

OtherIncome::create([
    'school_id' => auth()->user()->school_id,
    'income_category_id' => $request->income_category_id,
    'amount' => $request->amount,
    'payment_method' => $request->payment_method,
    'income_date' => $request->income_date,
    'term_id'=> $request->term_id,
    'year' => $term->year, 
    'description' => $request->description,
    'created_by' => Auth::id(),
]);

// ✅ Notify admin
Auth::user()->notify(new OtherIncomeNotification(
    $incomeCategory->name,   // <-- source
    $request->description,
    $request->amount
));

return redirect()->route('other_incomes.index')->with('success', 'Income added successfully!');

    }

    public function edit(OtherIncome $other_income)
    {
        $this->authorizeSchool($other_income);

        $categories = IncomeCategory::where('school_id', auth()->user()->school_id)->get();
        return view('income.edit', compact('other_income', 'categories'));
    }

    public function update(Request $request, OtherIncome $other_income)
    {
        $this->authorizeSchool($other_income);

        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'income_date' => 'required|date',
        ]);

        $term = Term::findOrFail($request->term_id);

        $other_income->update([
            'income_category_id' => $request->income_category_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'income_date' => $request->income_date,
            'term_id' => $request->term_id,
             'year' => $term->year, 
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('other_incomes.index')->with('success', 'Income updated successfully!');
    }

    public function destroy(OtherIncome $other_income)
    {
        $this->authorizeSchool($other_income);

        $other_income->delete();

        return redirect()->route('other_incomes.index')->with('success', 'Income deleted successfully!');
    }

    private function authorizeSchool($model)
    {
        if ($model->school_id !== auth()->user()->school_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
