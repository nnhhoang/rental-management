<?php

use App\Http\Controllers\API\ApartmentController;
use App\Http\Controllers\API\ApartmentRoomController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\FeeCollectionController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\TenantController;
use App\Http\Controllers\API\UtilityController;
use App\Http\Controllers\API\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('locale', [ApartmentController::class, 'changeLocale']);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    
    // Add route for getting a token for web authenticated users
    Route::post('get-token', [AuthController::class, 'getToken'])->middleware('web');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

Route::prefix('auth/admin')->group(function () {
    Route::post('login', [AuthController::class, 'adminLogin']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'adminLogout']);
        Route::get('profile', [AuthController::class, 'adminProfile']);
    });
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::prefix('apartments')->group(function () {
        Route::get('/', [ApartmentController::class, 'index']);
        Route::post('/', [ApartmentController::class, 'store']);
        Route::get('/user', [ApartmentController::class, 'userApartments']);
        Route::get('{id}', [ApartmentController::class, 'show']);
        Route::put('{id}', [ApartmentController::class, 'update']);
        Route::delete('{id}', [ApartmentController::class, 'destroy']);
    });

    Route::prefix('rooms')->group(function () {
        Route::get('/', [ApartmentRoomController::class, 'index']);
        Route::post('/', [ApartmentRoomController::class, 'store']);
        Route::get('/with-active-contract', [ApartmentRoomController::class, 'withActiveContract']);
        Route::get('/without-tenant', [ApartmentRoomController::class, 'withoutTenant']);
        Route::get('/apartments/{apartmentId}', [ApartmentRoomController::class, 'byApartment']);
        Route::get('{id}', [ApartmentRoomController::class, 'show']);
        Route::put('{id}', [ApartmentRoomController::class, 'update']);
        Route::delete('{id}', [ApartmentRoomController::class, 'destroy']);
    });

    Route::prefix('tenants')->group(function () {
        Route::get('/', [TenantController::class, 'index']);
        Route::post('/', [TenantController::class, 'store']);
        Route::get('/user', [TenantController::class, 'userTenants']);
        Route::get('/{id}/contracts', [TenantController::class, 'getContracts']);
        Route::get('/{id}', [TenantController::class, 'show']);
        Route::put('/{id}', [TenantController::class, 'update']);
        Route::delete('/{id}', [TenantController::class, 'destroy']);
    });

    Route::prefix('contracts')->group(function () {
        Route::get('/', [ContractController::class, 'index']);
        Route::post('/', [ContractController::class, 'store']);
        Route::get('/rooms/{roomId}/active', [ContractController::class, 'activeByRoom']);
        Route::get('/{id}', [ContractController::class, 'show']);
        Route::put('/{tenantContract}', [ContractController::class, 'update']);
        Route::patch('/{id}/terminate', [ContractController::class, 'terminate']);
        Route::delete('/{id}', [ContractController::class, 'destroy']);
    });

    Route::prefix('fees')->group(function () {
        Route::get('/', [FeeCollectionController::class, 'index']);
        Route::post('/', [FeeCollectionController::class, 'store']);
        Route::get('/unpaid', [FeeCollectionController::class, 'unpaid']);
        Route::get('/rooms/{roomId}', [FeeCollectionController::class, 'byRoom']);
        Route::get('/{id}', [FeeCollectionController::class, 'show']);
        Route::put('/{id}', [FeeCollectionController::class, 'update']);
        Route::patch('/{id}/payment', [FeeCollectionController::class, 'recordPayment']);
        Route::delete('/{id}', [FeeCollectionController::class, 'destroy']);
    });

    Route::prefix('utilities')->group(function () {
        Route::post('/electricity', [UtilityController::class, 'createElectricityUsage']);
        Route::post('/water', [UtilityController::class, 'createWaterUsage']);
        Route::get('/rooms/{roomId}/electricity/latest', [UtilityController::class, 'getLatestElectricity']);
        Route::get('/rooms/{roomId}/electricity', [UtilityController::class, 'getElectricityByDateRange']);
        Route::get('/rooms/{roomId}/water/latest', [UtilityController::class, 'getLatestWater']);
        Route::get('/rooms/{roomId}/water', [UtilityController::class, 'getWaterByDateRange']);
    });

    Route::prefix('statistics')->group(function () {
        Route::get('/unpaid-rooms', [StatisticsController::class, 'unpaidRooms']);
        Route::get('/monthly-fees', [StatisticsController::class, 'monthlyFeeStatistics']);
        Route::get('/dashboard', [StatisticsController::class, 'dashboard']);
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::get('/users/{id}', [AdminController::class, 'showUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    Route::get('/logs', [AdminController::class, 'logs']);
    Route::get('/statistics', [AdminController::class, 'statistics']);
    Route::get('/apartments', [AdminController::class, 'allApartments']);
    Route::get('/test', function() {
        return response()->json([
            'message' => 'Admin authentication successful',
            'user' => request()->user()
        ]);
    });
});