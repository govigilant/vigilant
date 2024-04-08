<?php

namespace Vigilant\Notifications\Tests\Notifications;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Jobs\SendNotificationJob;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Tests\Fakes\FakeChannel;
use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class NotificationTest extends TestCase
{
    #[Test]
    public function it_dispatches_notification_all_channels_job(): void
    {
        Bus::fake();

        Channel::withoutEvents(function (): void {
            Channel::query()->create([
                'team_id' => 1,
                'channel' => FakeChannel::class,
                'settings' => [],
            ]);

            Channel::query()->create([
                'team_id' => 1,
                'channel' => FakeChannel::class,
                'settings' => [],
            ]);

            // TODO: Uncomment after scopes are added
            //Channel::query()->create([
            //    'team_id' => 2,
            //    'channel' => FakeChannel::class,
            //    'settings' => [],
            //]);
        });

        Trigger::withoutEvents(function (): void {
            Trigger::query()->create([
                'team_id' => 1,
                'notification' => FakeNotification::class,
                'conditions' => [],
                'all_channels' => true,
            ]);

            Trigger::query()->create([
                'team_id' => 1,
                'notification' => FakeNotification::class,
                'conditions' => [],
                'all_channels' => true,
            ]);

        });

        FakeNotification::notify(1);

        Bus::assertDispatchedTimes(SendNotificationJob::class, 4);
    }

    #[Test]
    public function it_dispatches_notification_single_channels_job(): void
    {
        Bus::fake();

        Channel::withoutEvents(function (): void {
            Channel::query()->create([
                'team_id' => 1,
                'channel' => FakeChannel::class,
                'settings' => ['a'],
            ]);

            Channel::query()->create([
                'team_id' => 1,
                'channel' => FakeChannel::class,
                'settings' => ['b'],
            ]);
        });

        Trigger::withoutEvents(function (): void {
            /** @var Trigger $trigger */
            $trigger = Trigger::query()->create([
                'team_id' => 1,
                'notification' => FakeNotification::class,
                'conditions' => [],
                'all_channels' => false,
            ]);

            $trigger->channels()->sync([Channel::query()->first()?->id ?? 1]);
        });

        FakeNotification::notify(1);

        Bus::assertDispatched(SendNotificationJob::class, function(SendNotificationJob $job): bool {
            return $job->trigger->notification === FakeNotification::class &&
                $job->channel->settings === ['a'];
        });
    }

    #[Test]
    public function it_it_arrayable(): void
    {
        $notification = FakeNotification::make(1);

        $this->assertEquals([
            'title' => 'Title of this fake notification',
            'description' => 'Description of this fake notification',
            'level' => Level::Critical,
        ], $notification->toArray());
    }
}
