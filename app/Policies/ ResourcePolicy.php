<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourcePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user instanceof Admin) {
            return true;
        }
    }

    private function userOwnsResource(User $user, Model $resource)
    {
        if (method_exists($resource, 'apartment') && $resource->apartment) {
            return $user->id === $resource->apartment->user_id;
        }

        if (method_exists($resource, 'room') && $resource->room && $resource->room->apartment) {
            return $user->id === $resource->room->apartment->user_id;
        }

        if (property_exists($resource, 'apartment_room_id')) {
            $room = \App\Models\ApartmentRoom::find($resource->apartment_room_id);
            if ($room && $room->apartment) {
                return $user->id === $room->apartment->user_id;
            }
        }
        
        return false;
    }

    public function view(User $user, Model $resource)
    {
        return $this->userOwnsResource($user, $resource);
    }

    public function create(User $user, Model $resource)
    {
        return $this->userOwnsResource($user, $resource);
    }

    public function update(User $user, Model $resource)
    {
        return $this->userOwnsResource($user, $resource);
    }

    public function delete(User $user, Model $resource)
    {
        return $this->userOwnsResource($user, $resource);
    }
}