<?php

namespace Vigilant\Frontend\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use League\ISO3166\ISO3166;

class CountryCode implements ValidationRule
{
    public function __construct(
        protected string $format = 'alpha2',
        protected ?ISO3166 $iso3166 = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail(__('The :attribute field must be a valid country code.'));

            return;
        }

        $value = strtoupper(trim($value));

        /** @var ISO3166 $iso3166 */
        $iso3166 = $this->iso3166 ??= new ISO3166;

        try {
            match ($this->format) {
                'alpha3' => $iso3166->alpha3($value),
                'numeric' => $iso3166->numeric($value),
                default => $iso3166->alpha2($value),
            };
        } catch (\Throwable) {
            $fail(__('The :attribute field must be a valid country code.'));
        }
    }
}
