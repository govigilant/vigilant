<?php

namespace Vigilant\Core\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Vigilant\Core\Http\Exceptions\SsrfException;
use Vigilant\Core\Http\SsrfGuard;

class NotInternalUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        /** @var SsrfGuard $guard */
        $guard = app(SsrfGuard::class);

        try {
            $guard->assertSafeUrl($value);
        } catch (SsrfException $exception) {
            $fail($exception->getMessage());
        }
    }
}
