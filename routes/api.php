<?php

use App\Http\Controllers\API\ApartmentController;
use App\Http\Controllers\API\ApartmentRoomController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\FeeCollectionController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\TenantController;
use App\Http\Controllers\API\UtilityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    
    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// Protected API routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Apartments
    Route::get('apartments', [ApartmentController::class, 'index']);
    Route::post('apartments', [ApartmentController::class, 'store']);
    Route::get('user/apartments', [ApartmentController::class, 'userApartments']);
    
    // Apartment routes that need ownership check
    Route::middleware('check.apartment.ownership')->group(function() {
        Route::get('apartments/{id}', [ApartmentController::class, 'show']);
        Route::put('apartments/{id}', [ApartmentController::class, 'update']);
        Route::delete('apartments/{id}', [ApartmentController::class, 'destroy']);
    });
    
    // Rooms
    Route::get('rooms', [ApartmentRoomController::class, 'index']);
    Route::post('rooms', [ApartmentRoomController::class, 'store']);
    Route::get('apartments/{apartmentId}/rooms', [ApartmentRoomController::class, 'byApartment']);
    Route::get('rooms/with-active-contract', [ApartmentRoomController::class, 'withActiveContract']);
    Route::get('rooms/without-tenant', [ApartmentRoomController::class, 'withoutTenant']);
    
    // Room routes that need ownership check
    Route::middleware('check.room.ownership')->group(function() {
        Route::get('rooms/{id}', [ApartmentRoomController::class, 'show']);
        Route::put('rooms/{id}', [ApartmentRoomController::class, 'update']);
        Route::delete('rooms/{id}', [ApartmentRoomController::class, 'destroy']);
    });
    
    // Tenants
    Route::get('tenants', [TenantController::class, 'index']);
    Route::post('tenants', [TenantController::class, 'store']);
    Route::get('tenants/{id}', [TenantController::class, 'show']);
    Route::put('tenants/{id}', [TenantController::class, 'update']);
    Route::delete('tenants/{id}', [TenantController::class, 'destroy']);
    Route::get('user/tenants', [TenantController::class, 'userTenants']);
    
    // Contracts
    Route::get('contracts', [ContractController::class, 'index']);
    Route::post('contracts', [ContractController::class, 'store']);
    Route::get('contracts/active', [ContractController::class, 'active']);
    Route::get('rooms/{roomId}/active-contract', [ContractController::class, 'activeByRoom']);
    Route::get('tenants/{tenantId}/contracts', [ContractController::class, 'tenantHistory']);
    
    // Contract routes that need ownership check
    Route::middleware('check.contract.ownership')->group(function() {
        Route::get('contracts/{id}', [ContractController::class, 'show']);
        Route::put('contracts/{id}', [ContractController::class, 'update']);
        Route::patch('contracts/{id}/terminate', [ContractController::class, 'terminate']);
        Route::delete('contracts/{id}', [ContractController::class, 'destroy']);
    });
    
    // Fee Collections
    Route::get('fees', [FeeCollectionController::class, 'index']);
    Route::post('fees', [FeeCollectionController::class, 'store']);
    Route::get('rooms/{roomId}/fees', [FeeCollectionController::class, 'byRoom']);
    Route::get('fees/unpaid', [FeeCollectionController::class, 'unpaid']);
    
    // Fee routes that need ownership check
    Route::middleware('check.fee.ownership')->group(function() {
        Route::get('fees/{id}', [FeeCollectionController::class, 'show']);
        Route::put('fees/{id}', [FeeCollectionController::class, 'update']);
        Route::patch('fees/{id}/payment', [FeeCollectionController::class, 'recordPayment']);
        Route::delete('fees/{id}', [FeeCollectionController::class, 'destroy']);
    });
    
    // Utilities
    // Note: These might need room ownership checks too
    Route::middleware('check.room.ownership')->group(function() {
        Route::get('rooms/{roomId}/electricity/latest', [UtilityController::class, 'getLatestElectricity']);
        Route::get('rooms/{roomId}/electricity', [UtilityController::class, 'getElectricityByDateRange']);
        Route::get('rooms/{roomId}/water/latest', [UtilityController::class, 'getLatestWater']);
        Route::get('rooms/{roomId}/water', [UtilityController::class, 'getWaterByDateRange']);
    });
    
    Route::post('electricity', [UtilityController::class, 'createElectricityUsage']);
    Route::post('water', [UtilityController::class, 'createWaterUsage']);
    
    // Statistics
    Route::get('statistics/unpaid-rooms', [StatisticsController::class, 'unpaidRooms']);
    Route::get('statistics/monthly-fees', [StatisticsController::class, 'monthlyFeeStatistics']);
    Route::get('statistics/dashboard', [StatisticsController::class, 'dashboard']);
});