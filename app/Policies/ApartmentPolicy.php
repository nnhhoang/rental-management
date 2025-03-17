<?php

namespace App\Policies;

use App\Models\Apartment;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApartmentPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user instanceof Admin) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return true;
    }
    
    public function view(User $user, Apartment $apartment)
    {
        return $user->id === $apartment->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Apartment $apartment)
    {
        return $user->id === $apartment->user_id;
    }

    public function delete(User $user, Apartment $apartment)
    {
        return $user->id === $apartment->user_id;
    }
}