<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for resource ownership
        Gate::define('manage-apartment', function ($user, $apartment) {
            return $user->id === $apartment->user_id;
        });

        Gate::define('manage-room', function ($user, $room) {
            return $user->id === $room->apartment->user_id;
        });

        Gate::define('manage-contract', function ($user, $contract) {
            return $user->id === $contract->room->apartment->user_id;
        });

        Gate::define('manage-fee', function ($user, $fee) {
            return $user->id === $fee->room->apartment->user_id;
        });
    }
}