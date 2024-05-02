<?php

namespace Vigilant\Notifications\Notifications;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Vigilant\Notifications\Conditions\ConditionEngine;
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

    public static function make(mixed ...$args): static
    {
        return new static(...$args);
    }

    public static function notify(mixed ...$args): void
    {
        $instance = new static(...$args);

        /** @var Collection<int, Trigger> $triggers */
        $triggers = Trigger::query()
            ->with('channels')
            ->where('enabled', '=', true)
            ->where('notification', '=', static::class)
            ->get();

        /** @var ConditionEngine $conditionEngine */
        $conditionEngine = app(ConditionEngine::class);

        foreach ($triggers as $trigger) {

            if (! $conditionEngine->checkGroup($instance, $trigger->conditions,
                $trigger->conditions['operator'] ?? 'any')) {
                continue;
            }

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

    public function viewUrl(): ?string
    {
        return null;
    }

    public function url(): ?string
    {
        return null;
    }

    public function urlTitle(): ?string
    {
        return null;
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
