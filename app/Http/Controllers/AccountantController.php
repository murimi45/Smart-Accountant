<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // assuming accountants are users with role 'accountant'
use Illuminate\Support\Facades\Hash;

class AccountantController extends Controller
{
    // List all accountants
    public function index()
    {
        $accountants = User::where('role', 'accountant')
            ->where('school_id', auth()->user()->school_id)
            ->get();
        return view('accountants.index', compact('accountants'));
    }

    // Show add form
    public function create()
    {
        return view('accountants.create');
    }

    // Store new accountant
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'accountant',
            'school_id' => auth()->user()->school_id,
        ]);

        return redirect()->route('accountants.index')->with('success', 'Accountant added successfully.');
    }

    // Show edit form
    public function edit(User $accountant)
    {
        return view('accountants.edit', compact('accountant'));
    }

    // Update accountant
    public function update(Request $request, User $accountant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $accountant->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $accountant->name = $request->name;
        $accountant->email = $request->email;

        if ($request->password) {
            $accountant->password = Hash::make($request->password);
        }

        $accountant->save();

        return redirect()->route('accountants.index')->with('success', 'Accountant updated successfully.');
    }

    // Delete accountant
    public function destroy(User $accountant)
    {
        $accountant->delete();
        return redirect()->route('accountants.index')->with('success', 'Accountant deleted successfully.');
    }
}
