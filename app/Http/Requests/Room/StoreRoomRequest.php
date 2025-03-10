<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'apartment_id' => 'required|exists:apartments,id',
            'room_number' => 'required|string|max:45',
            'default_price' => 'required|numeric|min:0',
            'max_tenant' => 'nullable|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ];
    }
}