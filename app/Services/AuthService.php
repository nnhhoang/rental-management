<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     */
    public function register(array $data): User
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Attempt to login a user
     */
    public function login(array $credentials): array
    {
        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();

            // Revoke any existing tokens
            $user->tokens()->delete();

            // Create a new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'user' => $user,
                'token' => $token,
            ];
        }

        return [
            'success' => false,
        ];
    }

    /**
     * Logout the current user
     */
    public function logout(): bool
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $user->tokens()->delete();
        }

        Auth::guard('web')->logout();

        return true;
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(array $data): string
    {
        try {
            DB::beginTransaction();

            $status = Password::reset(
                $data,
                function (User $user, string $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            DB::commit();

            return $status;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
