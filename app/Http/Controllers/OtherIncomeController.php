<?php

namespace App\Http\Controllers;

use App\Models\OtherIncome;
use App\Models\IncomeCategory;
use App\Models\Term;
use App\Support\TenantRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OtherIncomeNotification;

class OtherIncomeController extends Controller
{
    public function index()
    {
        $incomes = OtherIncome::with('category')->latest()->get();

        return view('income.index', compact('incomes'));
    }

    public function create()
    {
        $categories = IncomeCategory::orderBy('name')->get();
        $terms = Term::with('academicYear')->orderByDesc('start_date')->get();

        return view('income.create', compact('categories', 'terms'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', OtherIncome::class);

        $request->validate([
            'income_category_id' => ['required', TenantRules::incomeCategories()],
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'income_date' => 'required|date',
            'term_id' => ['required', TenantRules::terms()],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $term = Term::forSchool()->findOrFail($request->term_id);
        $incomeCategory = IncomeCategory::forSchool()->findOrFail($request->income_category_id);

        OtherIncome::create([
            'income_category_id' => $request->income_category_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'income_date' => $request->income_date,
            'term_id' => $request->term_id,
            'year' => $term->year,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        Auth::user()->notify(new OtherIncomeNotification(
            $incomeCategory->name,
            $request->description,
            $request->amount
        ));

        return redirect()->route('other_incomes.index')->with('success', 'Income added successfully!');
    }

    public function edit($id)
    {
        $other_income = OtherIncome::forSchool()->findOrFail($id);
        $this->authorize('update', $other_income);
        $categories = IncomeCategory::orderBy('name')->get();

        return view('income.edit', compact('other_income', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $other_income = OtherIncome::forSchool()->findOrFail($id);
        $this->authorize('update', $other_income);

        $request->validate([
            'income_category_id' => ['required', TenantRules::incomeCategories()],
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'income_date' => 'required|date',
            'term_id' => ['required', TenantRules::terms()],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $term = Term::forSchool()->findOrFail($request->term_id);

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

    public function destroy($id)
    {
        $other_income = OtherIncome::forSchool()->findOrFail($id);
        $this->authorize('delete', $other_income);
        $other_income->delete();

        return redirect()->route('other_incomes.index')->with('success', 'Income deleted successfully!');
    }
}
