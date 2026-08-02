<?php

namespace App\Rules;

use App\Support\PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    public function __construct(private string $defaultRegion = PhoneNumber::DEFAULT_REGION) {}

    /**
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! PhoneNumber::isValid(is_string($value) ? $value : null, $this->defaultRegion)) {
            $fail('Проверьте номер телефона и код страны: такого номера не существует.');
        }
    }
}
