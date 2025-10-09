<?php

namespace Vigilant\Uptime\Data;

class UptimeResult
{
    public function __construct(
        public bool $up = true,
        public float $totalTime = 0,
        public ?string $country = null,
        public array $data = [],
    ) {}
}
