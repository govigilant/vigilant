<?php

namespace Vigilant\Uptime\Actions\Outpost;

use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class DetermineOutpost
{
    public function determine(): ?Outpost
    {
        return Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->inRandomOrder()
            ->first();
    }
}
