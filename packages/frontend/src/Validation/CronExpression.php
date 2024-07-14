<?php

namespace Vigilant\Frontend\Validation;

use Closure;
use Cron\CronExpression as Cron;
use Illuminate\Contracts\Validation\ValidationRule;

class CronExpression implements ValidationRule
{
    public function validate(string $attribute, $value, Closure $fail): void
    {
        if (Cron::isValidExpression($value) === false) {
            $fail("The $attribute field is not a valid cron expression.");
        }
    }
}
