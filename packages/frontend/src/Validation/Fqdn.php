<?php

namespace Vigilant\Frontend\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Fqdn implements ValidationRule
{
    public function __construct(protected bool $allowSubdomains = true)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->allowSubdomains) {
            $pattern = '/^(?:[a-z0-9-]+\.)*[a-z0-9-]+\.[a-z]{2,}$/i';
        } else {
            $pattern = '/^[a-z0-9-]+\.[a-z]{2,}$/i';
        }

        if (! preg_match($pattern, $value)) {
            $fail(__('Invalid domain name, please enter a domain name + tld. For example: govigilant.io'));
        }
    }
}
