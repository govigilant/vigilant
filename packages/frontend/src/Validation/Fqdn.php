<?php

namespace Vigilant\Frontend\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Fqdn implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $value)) {
            $fail(__('Invalid domain name'));
        }
    }
}
