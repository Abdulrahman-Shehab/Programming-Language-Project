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
                'status' => 'pending', // Set status to pending for admin approval
            ]);

            return response()->json([
                'message' => 'تم تسجيل طلبك بنجاح. يمكنك تسجيل الدخول عند الموافقة عليه من قبل الإدارة.',
                'user' => new UserResource($user)
            ], 201);

    }

    // Login
    public function login(LoginUserRequest $request)
    {
            if (!Auth::attempt($request->only('phone', 'password'))) {
                return response()->json(['message' => 'رقم الهاتف أو كلمة المرور غير صحيحة'], 401);
            }

            $user = Auth::user();

            // التحقق من حالة المستخدم
            if ($user->status !== 'approved') {
                if ($user->status === 'pending') {
                    return response()->json([
                        'message' => 'لم يتم قبول طلبك بعد. الرجاء الانتظار حتى موافقة الإدارة.'
                    ], 403);
                } elseif ($user->status === 'rejected') {
                    return response()->json([
                        'message' => 'تم رفض طلبك. لا يمكن تسجيل الدخول.'
                    ], 403);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'تم تسجيل الدخول بنجاح',
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
