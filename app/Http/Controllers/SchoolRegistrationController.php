<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|string|email|max:255|unique:schools',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $school = School::create([
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'address' => $validatedData['address'],
            'email' => $validatedData['email'],
        ]);

        $user = User::create([
            'name' => $validatedData['name'] . ' Admin',
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'super_admin',
        ]);

        return response()->json([
            'message' => 'School registered successfully',
            'school' => $school,
            'user' => $user,
        ], 201);
    }
}
