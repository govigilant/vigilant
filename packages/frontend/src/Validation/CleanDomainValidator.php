<?php

namespace Vigilant\Frontend\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CleanDomainValidator implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $value = (string) $value;

        if ($this->containsUrlSpecificCharacters($value)) {
            $fail(__('Please enter only the domain (e.g., govigilant.io)'));
        }
    }

    private function containsUrlSpecificCharacters(string $value): bool
    {
        return strpbrk($value, '/:#?') !== false;
    }
}
