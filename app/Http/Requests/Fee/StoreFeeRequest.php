<?php

namespace App\Http\Requests\Fee;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeRequest extends FormRequest
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

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'apartment_room_id' => trans('validation.attributes.apartment_id'),
            'electricity_number_before' => trans('validation.attributes.electricity_number_before'),
            'electricity_number_after' => trans('validation.attributes.electricity_number_after'),
            'water_number_before' => trans('validation.attributes.water_number_before'),
            'water_number_after' => trans('validation.attributes.water_number_after'),
            'charge_date' => trans('validation.attributes.charge_date'),
            'total_price' => trans('validation.attributes.total_price'),
            'total_paid' => trans('validation.attributes.total_paid'),
        ];
    }
}
