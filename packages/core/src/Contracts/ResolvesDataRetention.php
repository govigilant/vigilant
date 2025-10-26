<?php

namespace Vigilant\Core\Contracts;

use Illuminate\Support\Carbon;

interface ResolvesDataRetention
{
    public function resolve(string $class): Carbon;
}
