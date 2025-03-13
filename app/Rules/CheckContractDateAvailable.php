<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TenantContract;
use App\Models\ApartmentRoom;
use Carbon\Carbon;

class CheckContractDateAvailable implements ValidationRule
{
    protected $roomId;

    public function __construct(?int $roomId = null)
    {
        $this->roomId = $roomId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startDate = $value; 
        $room = ApartmentRoom::find($this->roomId);

        if (!$room) {
            $fail(trans('messages.room.not_found'));
            return;
        }

        if ($room->apartment && $room->apartment->user_id !== auth()->id()) {
            $fail(trans('messages.room.no_permission'));
            return;
        }

        $existingContract = TenantContract::where('apartment_room_id', $this->roomId)
            ->where('end_date', '>', $startDate)
            ->first();

        if ($existingContract) {
            $fail(trans('messages.contract.date_not_available', [
                'end_date' => $existingContract->end_date->format('d/m/Y')
            ]));
        }
    }
}

