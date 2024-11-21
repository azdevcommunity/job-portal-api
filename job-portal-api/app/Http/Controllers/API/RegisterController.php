<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Register method
    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|in:admin,company',
            'company_id' => 'nullable|exists:companies,id', // Validate company_id only if provided
        ]);

        // Check if the user has the 'company' role
        if ($request->role === 'company' && !$request->company_id) {
            return response()->json(['message' => 'Company users must provide a valid company_id'], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $request->role === 'company' ? $request->company_id : null, // Set company_id only for company users
        ]);

        // Generate an access token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return a success response
        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function registerCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'company',
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => $request->company_name,
            'email' => $request->email,
            'description' => $request->company_description,
        ]);

        $user->update(['company_id' => $company->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Company registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

//    public function registerJobSeeker(Request $request)
//    {
//        $request->validate([
//            'email' => 'required|email|unique:users,email',
//            'password' => 'required|string|confirmed',
//        ]);
//
//        // Create the user with the 'job_seeker' role
//        $user = User::create([
//            'email' => $request->email,
//            'password' => Hash::make($request->password),
//            'role' => 'job_seeker',
//        ]);
//
//        // Generate a token for the registered user
//        $token = $user->createToken('auth_token')->plainTextToken;
//
//        return response()->json([
//            'message' => 'User registered successfully',
//            'user' => $user,
//            'access_token' => $token,
//            'token_type' => 'Bearer',
//        ], 201);
//    }

}
