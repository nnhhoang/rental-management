<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TenantContract;

class CheckContractDateAvailable implements ValidationRule
{
    protected $roomId;
    protected $endDate;

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

        $this->endDate = request()->input('end_date');
        if (!$this->endDate) {
            return;
        }

        $conflictingContracts = TenantContract::where('apartment_room_id', $this->roomId)
            ->where(function ($query) use ($value) {
                $query->where(function ($q) use ($value) {
                    $q->where('start_date', '<=', $value)
                      ->where('end_date', '>', $value);
                })->orWhere(function ($q) {
                    $q->where('start_date', '<', $this->endDate)
                      ->where('end_date', '>=', $this->endDate);
                })->orWhere(function ($q) use ($value) {
                    $q->where('start_date', '>=', $value)
                      ->where('end_date', '<=', $this->endDate);
                })->orWhere(function ($q) use ($value) {
                    $q->where('start_date', '<=', $value)
                      ->where('end_date', '>=', $this->endDate);
                });
            })
            ->first();

        if ($conflictingContracts) {
            $fail(trans('messages.contract.date_conflict', [
                'start_date' => $conflictingContracts->start_date->format('d/m/Y'),
                'end_date' => $conflictingContracts->end_date->format('d/m/Y')
            ]));
        }
    }
}