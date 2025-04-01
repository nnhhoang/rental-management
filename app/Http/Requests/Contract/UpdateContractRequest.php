<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PhoneNumber;
use App\Rules\IdCard;
use Illuminate\Validation\Rule;

class UpdateContractRequest extends FormRequest
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
            'tel' => [
                'required',
                new PhoneNumber,
            ],
            'email' => 'required|email|max:256', Rule::unique('tenants', 'email')->ignore($this->tenant->id),
            'identity_card_number' => [
                'required',
                new IdCard,
                Rule::unique('tenants', 'identity_card_number')->ignore($this->tenant->id)
            ],
            'pay_period' => 'required|integer|in:3,6,12',
            'price' => 'required|numeric|min:0',
            'electricity_pay_type' => 'required|integer|in:1,2,3',
            'electricity_price' => 'required|numeric|min:0',
            'electricity_number_start' => 'required|integer|min:0',
            'water_pay_type' => 'required|integer|in:1,2,3',
            'water_price' => 'required|numeric|min:0',
            'water_number_start' => 'required|integer|min:0',
            'number_of_tenant_current' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date',
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
            'tenant.name' => trans('validation.tenant_name'),
            'tenant.tel' => trans('validation.tel'),
            'tenant.email' => trans('validation.email'),
            'tenant.identity_card_number' => trans('validation.identity_card_number'),
            'pay_period' => trans('validation.pay_period'),
            'price' => trans('validation.price'),
            'electricity_pay_type' => trans('validation.electricity_pay_type'),
            'electricity_price' => trans('validation.electricity_price'),
            'electricity_number_start' => trans('validation.electricity_number_start'),
            'water_pay_type' => trans('validation.water_pay_type'),
            'water_price' => trans('validation.water_price'),
            'water_number_start' => trans('validation.water_number_start'),
            'number_of_tenant_current' => trans('validation.number_of_tenant_current'),
            'note' => trans('validation.note'),
            'start_date' => trans('validation.start_date'),
            'end_date' => trans('validation.end_date'),
        ];
    }
}
