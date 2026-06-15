<?php
namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use App\Support\TenantRules;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $categories = IncomeCategory::orderBy('name')->get();

        return view('income.incomecategories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', TenantRules::unique('income_categories', 'name')],
            'description' => 'nullable|string',
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        IncomeCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('income_categories.index')
            ->with('success', 'Income category added successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = IncomeCategory::forSchool()->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', TenantRules::unique('income_categories', 'name', $category->id)],
            'description' => 'nullable|string',
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('income_categories.index')
            ->with('success', 'Income category updated successfully.');
    }

    public function destroy($id)
    {
        IncomeCategory::forSchool()->findOrFail($id)->delete();

        return redirect()->route('income_categories.index')
            ->with('success', 'Income category deleted successfully.');
    }
}
