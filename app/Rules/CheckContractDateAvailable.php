<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TenantContract;

class CheckContractDateAvailable implements ValidationRule
{
    protected $roomId;

    public function __construct(?int $roomId = null)
    {
        $this->roomId = $roomId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->roomId) {
            $fail(trans('validation.required', ['attribute' => 'apartment_room_id']));
            return;
        }

        // Find the latest ending active contract for this room
        $latestContract = TenantContract::where('apartment_room_id', $this->roomId)
            ->where('end_date', '>', $value)
            ->orderBy('end_date', 'desc')
            ->first();

        if ($latestContract) {
            $fail(trans('messages.contract.date_not_available', [
                'end_date' => $latestContract->end_date->format('d/m/Y')
            ]));
        }
    }
}