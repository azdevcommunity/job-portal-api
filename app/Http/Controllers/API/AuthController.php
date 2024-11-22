<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    // Login method with access and refresh tokens
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Generate access token (short-lived)
        $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(1))->plainTextToken;

        // Generate refresh token (long-lived)
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 36000, // 15 minutes
            'role' => $user->role,
            'company_id' => $user->company_id
        ]);
    }

    // Refresh token method
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        // Retrieve the token using the refresh token string
        $refreshToken = $request->input('refresh_token');

        // Find the user by the refresh token
        $token = \Laravel\Sanctum\PersonalAccessToken::findToken($refreshToken);

        if (!$token) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        // Get the user from the token
        $user = $token->tokenable;

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Invalidate the old refresh token
        $token->delete();

        // Create new access and refresh tokens
        $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(600))->plainTextToken;
        $newRefreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(365))->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 500 * 60, // 15 minutes
        ]);
    }

    // Logout method (invalidate all tokens)
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
