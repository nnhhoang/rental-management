<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TenantContract;
use App\Models\ApartmentRoom;

class RoomAvailableForContract implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, mixed): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $activeContract = TenantContract::where('apartment_room_id', $value)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->exists();
        
        if ($activeContract) {
            $fail(trans('messages.contract.active_contract_exists'));
            return;
        }

        $room = ApartmentRoom::with('apartment')->find($value);
        if (!$room) {
            $fail(trans('messages.room.not_found'));
            return;
        }
        
        if ($room->apartment && $room->apartment->user_id !== auth()->id()) {
            $fail(trans('messages.room.no_permission'));
        }
    }
}