<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
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
            'room_number' => 'required|string|max:45',
            'default_price' => 'required|numeric|min:0',
            'max_tenant' => 'nullable|integer|min:1',
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
            'room_number' => trans('validation.attributes.room_number'),
            'default_price' => trans('validation.attributes.default_price'),
            'max_tenant' => trans('validation.attributes.max_tenant'),
            'image' => trans('validation.attributes.image'),
        ];
    }
}