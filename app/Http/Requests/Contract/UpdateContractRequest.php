<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

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
            'pay_period' => 'required|integer|in:1,3,6,12',
            'price' => 'required|numeric|min:0',
            'electricity_pay_type' => 'required|integer|in:1,2,3',
            'electricity_price' => 'required|numeric|min:0',
            'water_pay_type' => 'required|integer|in:1,2,3',
            'water_price' => 'required|numeric|min:0',
            'number_of_tenant_current' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ];
    }
}
