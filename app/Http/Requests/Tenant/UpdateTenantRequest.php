<?php

namespace App\Http\Requests\Tenant;

use App\Rules\VietnameseIdCard;
use App\Rules\VietnamesePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
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
            'tel' => ['required', 'string', 'max:45', new VietnamesePhoneNumber],
            'email' => 'required|email|max:256',
            'identity_card_number' => ['required', 'string', 'max:45', new VietnameseIdCard],
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
            'tel' => trans('validation.attributes.tel'),
            'email' => trans('validation.attributes.email'),
            'identity_card_number' => trans('validation.attributes.identity_card_number'),
        ];
    }
}
