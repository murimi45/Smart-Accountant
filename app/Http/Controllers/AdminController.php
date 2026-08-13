<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /** School-staff roles creatable from the Users UI (not platform). */
    public const MANAGEABLE_ROLES = [
        User::ROLE_ADMIN,
        User::ROLE_ACCOUNTANT,
        User::ROLE_TEACHER,
        User::ROLE_HR_MANAGER,
    ];

    public function index(Request $request)
    {
        $query = User::query()
            ->where('school_id', auth()->user()->school_id)
            ->whereIn('role', self::MANAGEABLE_ROLES);

        if ($request->filled('name')) {
            $query->where('admin_name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }

        if ($request->filled('role') && in_array($request->role, self::MANAGEABLE_ROLES, true)) {
            $query->where('role', $request->role);
        }

        $admins = $query->orderBy('admin_name')->paginate(10)->withQueryString();

        return view('admins.index', compact('admins'));
    }

    public function create() { return view('admins.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'admin_name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:30',
            'role' => ['required', Rule::in(self::MANAGEABLE_ROLES)],
            'school_id' => 'prohibited',
        ]);

        $user = new User([
            'admin_name'=>$request->admin_name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'phone' => $request->phone,
            'role' => strtolower($request->role),
        ]);
        $user->school_id = auth()->user()->school_id;
        $user->save();

        return redirect()->route('admins.index')->with('success','User added successfully');
    }

    public function edit(User $admin)
    {
        $this->authorize('update', $admin);

        return view('admins.create', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $this->authorize('update', $admin);

        $request->validate([
            'admin_name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email,'.$admin->id,
            'password'=>'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:30',
            'role' => ['required', Rule::in(self::MANAGEABLE_ROLES)],
        ]);

        $admin->admin_name = $request->admin_name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->role = strtolower($request->role);
        if($request->password) $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admins.index')->with('success','User updated successfully');
    }

    public function destroy(User $admin)
    {
        $this->authorize('delete', $admin);

        $admin->delete();
        return redirect()->route('admins.index')->with('success','User deleted successfully');
    }
}
