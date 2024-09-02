<?php

namespace Vigilant\Frontend\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Fqdn implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^[a-z0-9-]+\.[a-z]{2,}$/i', $value)) {
            $fail(__('Invalid domain name'));
        }
    }
}
