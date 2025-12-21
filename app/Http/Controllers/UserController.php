<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Register
    public function register(RegisterUserRequest $request)
    {
            $profilePath = null;
            $idCardPath = null;

            if ($request->hasFile('profile_image')) {
                $profilePath = $request->file('profile_image')->store('profiles', 'public');
            }

            if ($request->hasFile('id_card')) {
                $idCardPath = $request->file('id_card')->store('id_cards', 'public');
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'password' => Hash::make($request->password),
                'profile_image' => $profilePath,
                'id_card' => $idCardPath,
            ]);

            return response()->json([
                'message' => 'Registration successful',
                'user' => new UserResource($user)
            ], 201);

    }

    // Login
    public function login(LoginUserRequest $request)
    {
            if (!Auth::attempt($request->only('phone', 'password'))) {
                return response()->json(['message' => 'Invalid phone or password'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => new UserResource($user),
                'token' => $token
            ], 200);
    }

    // Logout
    public function logout(Request $request)
    {
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Logout successful']);

    }
}
