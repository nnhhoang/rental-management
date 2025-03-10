<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\VietnamesePhoneNumber;
use App\Rules\VietnameseIdCard;
use App\Rules\RoomAvailableForContract;

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
            'apartment_room_id' => [
                'required', 
                'exists:apartment_rooms,id',
                new RoomAvailableForContract()
            ],
            'is_create_tenant' => 'required|boolean',
            'tenant_id' => [
                'required_if:is_create_tenant,false',
                'exists:tenants,id',
                'nullable'
            ],
            'tenant_name' => 'required_if:is_create_tenant,true|string|max:45',
            'tel' => [
                'required_if:is_create_tenant,true',
                new VietnamesePhoneNumber
            ],
            'email' => 'required_if:is_create_tenant,true|email|max:256',
            'identity_card_number' => [
                'required_if:is_create_tenant,true',
                new VietnameseIdCard
            ],
            'pay_period' => 'required_unless:is_indefinite,true|nullable|integer|in:3,6,12',
            'price' => 'required|numeric|min:0',
            'electricity_pay_type' => 'required|integer|in:1,2,3',
            'electricity_price' => 'required|numeric|min:0',
            'electricity_number_start' => 'required|integer|min:0',
            'water_pay_type' => 'required|integer|in:1,2,3',
            'water_price' => 'required|numeric|min:0',
            'water_number_start' => 'required|integer|min:0',
            'number_of_tenant_current' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'start_date' => 'nullable|date|date_format:Y-m-d|before_or_equal:'.now()->format('Y-m-d'),
            'end_date' => 'nullable|date|date_format:Y-m-d|after:start_date',
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->mergeDefaultValues();
    }
    
    /**
     * Merge default values if not provided
     */
    protected function mergeDefaultValues()
    {
        if (!$this->has('start_date')) {
            $this->merge([
                'start_date' => now()->format('Y-m-d')
            ]);
        }
        if ($this->has('is_indefinite') && $this->is_indefinite === true) {
            $this->merge(['end_date' => null]);
        }
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'apartment_room_id' => trans('validation.apartment_room_id'),
            'is_create_tenant' => trans('validation.is_create_tenant'),
            'tenant_id' => trans('validation.tenant_id'),
            'tenant_name' => trans('validation.tenant_name'),
            'tel' => trans('validation.tel'),
            'email' => trans('validation.email'),
            'id_card' => trans('validation.id_card'),
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