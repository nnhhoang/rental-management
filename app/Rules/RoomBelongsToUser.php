<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\ApartmentRoom;

class RoomBelongsToUser implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $room = ApartmentRoom::with('apartment')->find($value);

        if (!$room) {
            $fail(trans('messages.room.not_found'));
            return;
        }

        if (!$room->apartment || $room->apartment->user_id !== auth()->id()) {
            $fail(trans('messages.room.no_permission'));
            return;
        }
    }
}