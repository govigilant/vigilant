<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Jobs\CheckUnavailableOutpostJob;
use Vigilant\Uptime\Models\Outpost;

class CheckUnavailableOutpostsCommand extends Command
{
    protected $signature = 'uptime:check-unavailable-outposts';

    protected $description = 'Check unavailable outposts and remove them if still unreachable after 15 minutes';

    public function handle(): int
    {
        $outposts = Outpost::query()
            ->where('status', '=', OutpostStatus::Unavailable)
            ->whereNotNull('unavailable_at')
            ->where('unavailable_at', '<=', now()->subMinutes(15))
            ->get();

        foreach ($outposts as $outpost) {
            CheckUnavailableOutpostJob::dispatch($outpost);
        }

        return static::SUCCESS;
    }
}
