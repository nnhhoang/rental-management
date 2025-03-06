<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'apartment_room_id' => 'required|exists:apartment_rooms,id',
            'electricity_number_before' => 'required|integer|min:0',
            'electricity_number_after' => 'required|integer|min:0',
            'water_number_before' => 'required|integer|min:0',
            'water_number_after' => 'required|integer|min:0',
            'charge_date' => 'required|date',
            'total_price' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0',
        ];
    }
}
