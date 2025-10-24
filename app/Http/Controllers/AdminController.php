<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role','admin')
            ->where('school_id', auth()->user()->school_id)
            ->get();
        return view('admins.index', compact('admins'));
    }

    public function create() { return view('admins.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6|confirmed'
        ]);

        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>'admin',
            'school_id'=>auth()->user()->school_id
        ]);

        return redirect()->route('admins.index')->with('success','Admin added successfully');
    }

    public function edit(User $admin){ return view('admins.edit', compact('admin')); }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email,'.$admin->id,
            'password'=>'nullable|string|min:6|confirmed'
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        if($request->password) $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admins.index')->with('success','Admin updated successfully');
    }

    public function destroy(User $admin)
    {
        $admin->delete();
        return redirect()->route('admins.index')->with('success','Admin deleted successfully');
    }
}

