<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IdCard implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, mixed): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^[0-9]{9,12}$/', $value)) {
            $fail(trans('validation.id_card.invalid_format'));
        }
    }
}