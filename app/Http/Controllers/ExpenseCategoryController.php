<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Support\TenantRules;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ExpenseCategory::class);

        $categories = ExpenseCategory::latest()->paginate(10);

        return view('expenses.categories
        ', compact('categories'));
    }

    public function create()
    {
        return view('expense_categories.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', ExpenseCategory::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255', TenantRules::unique('expense_categories', 'name')],
            'description' => 'nullable|string',
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        ExpenseCategory::create($request->only('name', 'description'));

        return redirect()->route('expense_categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        ExpenseCategory::forSchool()->findOrFail($expenseCategory->id);
        $this->authorize('update', $expenseCategory);

        return view('expense_categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, $id)
    {
        $expenseCategory = ExpenseCategory::forSchool()->findOrFail($id);
        $this->authorize('update', $expenseCategory);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                TenantRules::unique('expense_categories', 'name', $expenseCategory->id),
            ],
            'description' => 'nullable|string',
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $expenseCategory->update($request->only('name', 'description'));

        return redirect()->route('expense_categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy($id)
    {
        $expenseCategory = ExpenseCategory::forSchool()->findOrFail($id);
        $this->authorize('delete', $expenseCategory);

        $expenseCategory->delete();

        return redirect()->route('expense_categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}
