<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user)
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result['success']) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($result['user']),
            'token' => $result['token']
        ]);
    }

    public function logout()
    {
        $this->authService->logout();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = $this->authService->forgotPassword($request->email);

        return response()->json([
            'message' => 'Password reset link sent'
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->authService->resetPassword($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ));

        return response()->json([
            'message' => 'Password has been reset'
        ]);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }
}