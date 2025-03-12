<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\VietnamesePhoneNumber;
use App\Rules\VietnameseIdCard;
use App\Rules\RoomAvailableForContract;

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
            'start_date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date',
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
        if (!$this->filled('start_date')) {
            $this->merge([
                'start_date' => now()->format('Y-m-d')
            ]);
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