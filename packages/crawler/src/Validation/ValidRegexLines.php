<?php

namespace Vigilant\Crawler\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRegexLines implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        collect(explode("\n", (string) $value))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->each(function (string $line) use ($fail): void {
                if (@preg_match($line, '') === false) {
                    $fail(__('One or more URL blacklist patterns are not valid regular expressions.'));
                }
            });
    }
}
