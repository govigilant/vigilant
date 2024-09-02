<?php

namespace Vigilant\Notifications\Commands;

use Illuminate\Console\Command;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Jobs\SendNotificationJob;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\TestNotification;

class TestNotificationCommand extends Command
{
    protected $signature = 'notifications:test-channel {channelId}';

    protected $description = 'Test a notification channel';

    public function handle(): int
    {
        /** @var ?int $channelId */
        $channelId = $this->argument('channelId');

        /** @var Channel $channel */
        $channel = Channel::query()->withoutGlobalScopes()->findOrFail($channelId);

        foreach (Level::cases() as $level) {
            SendNotificationJob::dispatchSync(
                new TestNotification($level),
                $channel->team_id,
                $channel->id
            );
        }

        return static::SUCCESS;
    }
}
