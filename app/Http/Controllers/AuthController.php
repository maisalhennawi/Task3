<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginRequest;
use App\Traits\FileTrait;
use App\Traits\ResponseTrait;
use App\Events\UserRegistered;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    use FileTrait, ResponseTrait;

    public function signup(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        // Handle file uploads
        $data['profile_photo'] = $this->uploadFile($request->file('profile_photo'), 'profile_photos');
        $data['certificate'] = $this->uploadFile($request->file('certificate'), 'certificates');

        $user = User::create($data);

        // Generate email verification code
        $user->email_verification_code = Str::random(6);
        $user->email_verification_expires_at = Carbon::now()->addMinutes(3);
        $user->save();

        // Send verification email
        event(new \App\Events\UserRegistered($user));

        return $this->successResponse('User registered successfully. Please check your email for verification code.', 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = Auth::user();
        if ($user->email_verified_at === null) {
            return $this->errorResponse('Email not verified', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse('Logged out successfully');
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::where('email_verification_code', $request->code)
                     ->where('email_verification_expires_at', '>', Carbon::now())
                     ->first();

        if (!$user) {
            return $this->errorResponse('Invalid or expired verification code', 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->email_verification_code = null;
        $user->email_verification_expires_at = null;
        $user->save();

        return $this->successResponse('Email verified successfully');
    }
}
