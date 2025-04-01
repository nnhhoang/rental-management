<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        
        return $this->successResponse(
            new UserResource($user),
            trans('messages.auth.registered'),
            201
        );
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result['success']) {
            return $this->errorResponse(
                trans('messages.auth.login_failed'),
                null,
                401
            );
        }

        return $this->successResponse([
            'user' => new UserResource($result['user']),
            'token' => $result['token']
        ], trans('messages.auth.login_success'));
    }

    public function logout()
    {
        $this->authService->logout();
        
        return $this->successResponse(
            null,
            trans('messages.auth.logout_success')
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->email);

        return $this->successResponse(
            null,
            trans('messages.auth.password_reset_link_sent')
        );
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ));

        return $this->successResponse(
            null,
            trans('messages.auth.password_reset_success')
        );
    }

    public function user()
    {
        return $this->successResponse(
            new UserResource(Auth::user())
        );
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        $result = $this->authService->adminLogin($request->validated());

        if (!$result['success']) {
            return $this->errorResponse(
                trans('messages.auth.login_failed'),
                null,
                401
            );
        }

        return $this->successResponse([
            'admin' => new AdminResource($result['admin']),
            'token' => $result['token']
        ], trans('messages.auth.login_success'));
    }

    public function adminLogout()
    {
        $this->authService->adminLogout();
        
        return $this->successResponse(
            null,
            trans('messages.auth.logout_success')
        );
    }

    public function adminProfile()
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return $this->unauthorizedResponse();
        }
        
        return $this->successResponse(
            new AdminResource($admin)
        );
    }

    /**
     * Generate and return a token for a web-authenticated user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken()
    {
        // Check if the request has a valid session and the user is authenticated
        if (!Auth::check()) {
            return $this->errorResponse(
                'Not authenticated. Session may have expired.',
                null,
                401
            );
        }
        
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            // This should not happen if auth()->check() passes, but just in case
            return $this->errorResponse(
                'User not found in session',
                null,
                401
            );
        }
        
        // Log the successful authentication
        \Illuminate\Support\Facades\Log::info('Token generated for user', ['user_id' => $user->id, 'email' => $user->email]);
        
        // Revoke existing tokens
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user)
        ], 'Token generated successfully');
    }
}