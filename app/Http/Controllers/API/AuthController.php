<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
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

        if (! $result['success']) {
            return $this->errorResponse(
                trans('messages.auth.login_failed'),
                null,
                401
            );
        }

        return $this->successResponse([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
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
}
