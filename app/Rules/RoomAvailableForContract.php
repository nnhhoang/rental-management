<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TenantContract;
use App\Models\ApartmentRoom;
use Carbon\Carbon;

class RoomAvailableForContract implements ValidationRule
{
    protected $startDate;

    public function __construct(?string $startDate = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : now();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $room = ApartmentRoom::find($value);
        if (!$room) {
            $fail(trans('messages.room.not_found'));
            return;
        }

        if ($room->apartment && $room->apartment->user_id !== auth()->id()) {
            $fail(trans('messages.room.no_permission'));
            return;
        }

        $existingContract = $this->findActiveContract($value);
        if ($existingContract) {
            $fail(trans('messages.contract.time_overlap', [
                'start' => $existingContract->start_date->format('Y-m-d'),
                'end' => $existingContract->end_date ? $existingContract->end_date->format('Y-m-d') : trans('messages.contract.ongoing')
            ]));
        }
    }

    /**
     * Find contracts that are still active when the new contract starts.
     */
    protected function findActiveContract(int $roomId)
    {
        return TenantContract::where('apartment_room_id', $roomId)
            ->where('end_date', '>=', $this->startDate) 
            ->first();
    }
}
