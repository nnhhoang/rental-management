<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
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
     *
     * @param array $data
     * @return User
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
     *
     * @param array $credentials
     * @return array
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
                'token' => $token
            ];
        }
        
        return [
            'success' => false
        ];
    }

    /**
     * Logout the current user
     *
     * @return bool
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
     *
     * @param string $email
     * @return string
     */
    public function forgotPassword(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset user password
     *
     * @param array $data
     * @return string
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
    /**
     * Attempt to login an admin
     *
     * @param array $credentials
     * @return array
     */
    public function adminLogin(array $credentials): array
    {
        if (Auth::guard('admin')->attempt($credentials)) {
            /** @var Admin $admin */
            $admin = Auth::guard('admin')->user();
            
            // Revoke existing tokens
            $admin->tokens()->delete();
            
            // Create a new token
            $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;
            
            return [
                'success' => true,
                'admin' => $admin,
                'token' => $token
            ];
        }
        
        return [
            'success' => false
        ];
    }
    /**
     * Logout the current admin
     *
     * @return bool
     */
    public function adminLogout(): bool
    {
        if (Auth::guard('admin')->check()) {
            /** @var Admin $admin */
            $admin = Auth::guard('admin')->user();
            $admin->tokens()->delete();
        }
        
        Auth::guard('admin')->logout();
        
        return true;
    }   
}