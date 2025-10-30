<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Schools;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered school admin user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // ✅ Validation
        Validator::make($input, [
            'school_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('schools', 'email'),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ])->validate();

        // ✅ Create the School
        $school = Schools::create([
            'school_name' => $input['school_name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'address' => $input['address'] ?? null,
            'subscription_status' => 'inactive',
        ]);

        // ✅ Create the Admin User (linked to the school)
        $user = User::create([
            'admin_name' => $input['admin_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => 'admin',
            'school_id' => $school->id,
        ]);

        return $user; // ✅ Fortify auto-logs in the user after this
    }
}
     
    