<?php

namespace Vigilant\Notifications\Notifications;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Jobs\SendNotificationJob;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;

abstract class Notification implements Arrayable
{
    public static string $name = '';

    public string $title = '';

    public string $description = '';

    public Level $level = Level::Info;

    public static function notify(...$args): void
    {
        $instance = new static(...$args);

        /** @var Collection<int, Trigger> $triggers */
        $triggers = Trigger::query()
            ->with('channels')
            ->where('notification', '=', static::class)
            ->get();

        foreach ($triggers as $trigger) {

            // TODO: Check conditions

            $channels = $trigger->all_channels ? Channel::all() : $trigger->channels;

            foreach ($channels as $channel) {
                SendNotificationJob::dispatch($instance, $channel, $trigger);
            }
        }
    }

    abstract public function uniqueId(): string;

    public function level(): Level
    {
        return $this->level;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title(),
            'description' => $this->description(),
            'level' => $this->level(),
        ];
    }
}
