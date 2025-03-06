<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
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
            'name' => 'required|string|max:45',
            'address' => 'required|string|max:256',
            'province_id' => 'nullable|string|max:256',
            'district_id' => 'nullable|string|max:256',
            'ward_id' => 'nullable|string|max:256',
            'image' => 'nullable|image|max:2048',
        ];
    }
}