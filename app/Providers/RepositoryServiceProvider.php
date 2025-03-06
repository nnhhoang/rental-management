<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ApartmentRepositoryInterface;
use App\Repositories\Contracts\ApartmentRoomRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\TenantContractRepositoryInterface;
use App\Repositories\Contracts\RoomFeeCollectionRepositoryInterface;
use App\Repositories\Contracts\ElectricityUsageRepositoryInterface;
use App\Repositories\Contracts\WaterUsageRepositoryInterface;
use App\Repositories\Contracts\LogRepositoryInterface;

// Implementations
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\ApartmentRepository;
use App\Repositories\Eloquent\ApartmentRoomRepository;
use App\Repositories\Eloquent\TenantRepository;
use App\Repositories\Eloquent\TenantContractRepository;
use App\Repositories\Eloquent\RoomFeeCollectionRepository;
use App\Repositories\Eloquent\ElectricityUsageRepository;
use App\Repositories\Eloquent\WaterUsageRepository;
use App\Repositories\Eloquent\LogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ApartmentRepositoryInterface::class, ApartmentRepository::class);
        $this->app->bind(ApartmentRoomRepositoryInterface::class, ApartmentRoomRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(TenantContractRepositoryInterface::class, TenantContractRepository::class);
        $this->app->bind(RoomFeeCollectionRepositoryInterface::class, RoomFeeCollectionRepository::class);
        $this->app->bind(ElectricityUsageRepositoryInterface::class, ElectricityUsageRepository::class);
        $this->app->bind(WaterUsageRepositoryInterface::class, WaterUsageRepository::class);
        $this->app->bind(LogRepositoryInterface::class, LogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}