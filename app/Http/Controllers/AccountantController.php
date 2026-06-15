<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountantController extends Controller
{
    public function index()
    {
        $accountants = User::where('role', 'accountant')
            ->where('school_id', auth()->user()->school_id)
            ->paginate(10);
        return view('accountant.index', compact('accountants'));
    }

    public function create()
    {
        return view('accountant.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'school_id' => 'prohibited',
        ]);

        $user = new User([
            'admin_name' => $request->admin_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'accountant',
        ]);
        $user->school_id = auth()->user()->school_id;
        $user->save();

        return redirect()->route('accountants.index')->with('success', 'Accountant added successfully.');
    }

    public function edit(User $accountant)
    {
        $this->authorize('manageAccountant', $accountant);

        return view('accountant.edit', compact('accountant'));
    }

    public function update(Request $request, User $accountant)
    {
        $this->authorize('manageAccountant', $accountant);

        $request->validate([
            'admin_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $accountant->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $accountant->admin_name = $request->admin_name;
        $accountant->phone = $request->phone;
        $accountant->email = $request->email;

        if ($request->password) {
            $accountant->password = Hash::make($request->password);
        }

        $accountant->save();

        return redirect()->route('accountant.index')->with('success', 'Accountant updated successfully.');
    }

    public function destroy(User $accountant)
    {
        $this->authorize('manageAccountant', $accountant);

        $accountant->delete();
        return redirect()->route('accountant.index')->with('success', 'Accountant deleted successfully.');
    }
}
