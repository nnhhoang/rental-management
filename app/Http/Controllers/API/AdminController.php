<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\LogResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Apartment;
use App\Models\Log;
use App\Models\User;
use App\Repositories\Contracts\ApartmentRepositoryInterface;
use App\Repositories\Contracts\LogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\AuthService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends BaseController
{
    protected $userRepository;
    protected $apartmentRepository;
    protected $logRepository;
    protected $statisticsService;
    protected $authService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ApartmentRepositoryInterface $apartmentRepository,
        LogRepositoryInterface $logRepository,
        StatisticsService $statisticsService,
        AuthService $authService
    ) {
        $this->userRepository = $userRepository;
        $this->apartmentRepository = $apartmentRepository;
        $this->logRepository = $logRepository;
        $this->statisticsService = $statisticsService;
        $this->authService = $authService;
    }

    /**
     * Admin dashboard data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        $userCount = $this->userRepository->all()->count();
        $apartmentCount = $this->apartmentRepository->all()->count();

        $statistics = [
            'total_users' => $userCount,
            'total_apartments' => $apartmentCount,
            'total_active_contracts' => $this->statisticsService->getTotalActiveContracts(),
            'occupancy_rate' => $this->statisticsService->getOccupancyRate(),
        ];

        $recentLogs = $this->logRepository->getRecentLogs(10);
        
        return $this->successResponse([
            'statistics' => $statistics,
            'recent_activity' => LogResource::collection($recentLogs)
        ]);
    }
    
    /**
     * List all users
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUsers(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        
        if ($search) {
            $users = $this->userRepository->searchUsers($search, $perPage);
        } else {
            $users = $this->userRepository->paginate($perPage);
        }
        
        return $this->successResponse(
            UserResource::collection($users)
        );
    }

    /**
     * Create a new user
     * 
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        try {
            DB::beginTransaction();
            
            $user = $this->userRepository->create($data);

            $this->logRepository->createLog(
                auth()->id(),
                'user_created',
                "Admin created new user: {$user->name}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
            
            DB::commit();
            
            return $this->successResponse(
                new UserResource($user),
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'An error occurred while creating the user',
                null,
                500
            );
        }
    }

    /**
     * Show user details
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUser($id)
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }
        
        return $this->successResponse(
            new UserResource($user)
        );
    }

    /**
     * Update user details
     * 
     * @param UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(UpdateUserRequest $request, $id)
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }
        
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        try {
            DB::beginTransaction();
            
            $user = $this->userRepository->update($id, $data);

            $this->logRepository->createLog(
                auth()->id(),
                'user_updated',
                "Admin updated user: {$user->name}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
            
            DB::commit();
            
            return $this->successResponse(
                new UserResource($user),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'An error occurred while updating the user',
                null,
                500
            );
        }
    }

    /**
     * Delete a user
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }
        
        try {
            DB::beginTransaction();
            $this->logRepository->createLog(
                auth()->id(),
                'user_deleted',
                "Admin deleted user: {$user->name}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
            
            $this->userRepository->delete($id);
            
            DB::commit();
            
            return $this->successResponse(
                null,
                'User deleted successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'An error occurred while deleting the user. The user may have associated data.',
                null,
                500
            );
        }
    }

    /**
     * View system logs
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $action = $request->query('action');
        $userId = $request->query('user_id');
        
        if ($action) {
            $logs = $this->logRepository->getLogsByAction($action);
        } elseif ($userId) {
            $logs = $this->logRepository->getLogsByUser($userId);
        } else {
            $logs = $this->logRepository->paginate($perPage);
        }
        
        return $this->successResponse(
            LogResource::collection($logs)
        );
    }

    /**
     * System statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        $year = $request->query('year', date('Y'));
        
        $data = [
            'total_users' => User::count(),
            'total_admins' => Admin::count(),
            'total_apartments' => Apartment::count(),
            'total_active_contracts' => $this->statisticsService->getTotalActiveContracts(),
            'occupancy_rate' => $this->statisticsService->getOccupancyRate(),
            'income_statistics' => $this->statisticsService->getIncomeStatistics($year),
        ];
        
        return $this->successResponse($data);
    }

    /**
     * View all apartments (admin override)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allApartments(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        
        if ($search) {
            $apartments = $this->apartmentRepository->searchApartments($search, $perPage);
        } else {
            $apartments = $this->apartmentRepository->paginate($perPage);
        }
        
        return $this->successResponse(
            ApartmentResource::collection($apartments)
        );
    }
    
    /**
     * Create a new admin account
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAdmin(Request $request)
    {
        $validated = $request->validate([
            'admin_login_id' => 'required|string|max:64|unique:admins',
            'email' => 'required|email|max:256|unique:admins',
            'password' => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();
            
            $admin = Admin::create([
                'admin_uuid' => (string) Str::uuid(),
                'admin_login_id' => $validated['admin_login_id'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'remember_token' => Str::random(10),
            ]);

            $this->logRepository->createLog(
                auth()->id(),
                'admin_created',
                "Admin created new admin account: {$admin->admin_login_id}",
                ['admin_id' => $admin->id, 'email' => $admin->email]
            );
            
            DB::commit();
            
            return $this->successResponse(
                $admin,
                'Admin account created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'An error occurred while creating the admin account',
                null,
                500
            );
        }
    }
}