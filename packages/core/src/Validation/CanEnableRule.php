<?php

namespace Vigilant\Core\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CanEnableRule implements ValidationRule
{
    public function __construct(public string $model)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_bool($value) && ! $value) {
            return;
        }

        if (! auth()->user()->can('create', $this->model)) {
            $fail('Unable to enable this resource, check your billing plan.');
        }
    }
}
