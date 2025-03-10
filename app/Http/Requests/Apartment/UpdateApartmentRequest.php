<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
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
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => trans('validation.attributes.name'),
            'address' => trans('validation.attributes.address'),
            'province_id' => trans('validation.attributes.province_id'),
            'district_id' => trans('validation.attributes.district_id'),
            'ward_id' => trans('validation.attributes.ward_id'),
            'image' => trans('validation.attributes.image'),
        ];
    }
}