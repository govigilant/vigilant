<?php

namespace Vigilant\Core\Concerns;

use Illuminate\Support\Carbon;
use Vigilant\Core\Contracts\ResolvesDataRetention;

trait HasDataRetention
{
    protected function retentionPeriod(): Carbon
    {
        $resolver = app(ResolvesDataRetention::class);

        return $resolver->resolve(static::class);
    }
}
