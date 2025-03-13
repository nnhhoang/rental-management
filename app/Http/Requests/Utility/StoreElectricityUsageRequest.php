<?php

namespace App\Http\Requests\Utility;

use Illuminate\Foundation\Http\FormRequest;

class StoreElectricityUsageRequest extends FormRequest
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
            'usage_number' => 'required|integer|min:0',
            'input_date' => 'required|date',
            'image' => 'nullable|image|max:2048',
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
            'usage_number' => trans('validation.attributes.usage_number'),
            'input_date' => trans('validation.attributes.input_date'),
            'image' => trans('validation.attributes.image'),
        ];
    }
}
