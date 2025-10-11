<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class RemoveUnavailableOutpostsCommand extends Command
{
    protected $signature = 'uptime:remove-unavailable-outposts';

    protected $description = 'Remove unavailable outposts';

    public function handle(): int
    {
        Outpost::query()
            ->where('status', '=', OutpostStatus::Unavailable)
            ->where('updated_at', '<', now()->subHour(1))
            ->delete();

        return static::SUCCESS;
    }
}
