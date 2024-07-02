<?php

namespace Vigilant\Users\Validators;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Vigilant\Users\Models\User;

class RegistrationEnabledValidator implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! ce()) {
            return;
        }

        $userCount = User::query()->count();

        if ($userCount > 0) {
            $fail(__('Registration disabled, ask your administrator to create new accounts'));
        }
    }
}
