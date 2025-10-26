<?php

namespace Vigilant\Core\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Core\Contracts\ResolvesDataRetention;

class ResolveDataRetention implements ResolvesDataRetention
{
    public function resolve(string $class): Carbon
    {
        $days = config('core.data_retention.'.$class, 365);

        return now()->subDays($days);
    }
}
