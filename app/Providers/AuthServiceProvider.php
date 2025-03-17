<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\TenantContract;
use App\Models\RoomFeeCollection;
use App\Models\Admin;
use App\Policies\ApartmentPolicy;
use App\Policies\ResourcePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Apartment::class => ApartmentPolicy::class,
        ApartmentRoom::class => ResourcePolicy::class,
        TenantContract::class => ResourcePolicy::class,
        RoomFeeCollection::class => ResourcePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage-apartment', function ($user, $apartment) {
            if ($user instanceof Admin) {
                return true;
            }
            return $user->id === $apartment->user_id;
        });

        Gate::define('manage-room', function ($user, $room) {
            if ($user instanceof Admin) {
                return true;
            }
            return $user->id === $room->apartment->user_id;
        });

        Gate::define('manage-contract', function ($user, $contract) {
            if ($user instanceof Admin) {
                return true;
            }
            return $user->id === $contract->room->apartment->user_id;
        });

        Gate::define('manage-fee', function ($user, $fee) {
            if ($user instanceof Admin) {
                return true;
            }
            return $user->id === $fee->room->apartment->user_id;
        });
    }
}