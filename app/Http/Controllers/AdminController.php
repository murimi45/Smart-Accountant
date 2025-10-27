<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = $users = User::whereIn('role', ['Admin', 'Accountant'])

            ->where('school_id', auth()->user()->school_id)
            ->paginate(10);
        return view('admins.index', compact('admins'));
    }

    public function create() { return view('admins.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'admin_name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6|confirmed',
            'role' => 'required|in:admin,Accountant',
        ]);

        User::create([
            'admin_name'=>$request->admin_name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            
            'role' => $request->role,
            'school_id'=>auth()->user()->school_id
        ]);

        return redirect()->route('admins.index')->with('success','User added successfully');
    }

    public function edit(User $admin){ return view('admins.create', compact('admin')); }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'admin_name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email,'.$admin->id,
            'password'=>'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,Accountant',
        ]);

        $admin->admin_name = $request->admin_name;
        $admin->email = $request->email;
        $admin->role = $request->role;
        if($request->password) $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admins.index')->with('success','User updated successfully');
    }

    public function destroy(User $admin)
    {
        $admin->delete();
        return redirect()->route('admins.index')->with('success','User deleted successfully');
    }
}

