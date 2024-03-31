<?php

namespace Vigilant\Notifications\Channels;

use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;

abstract class NotificationChannel
{
    public static string $name = '';

    public array $rules = [];

    /** @var ?string Livewire component for configuring the channel */
    public static ?string $component = null;

    public function rules(): array
    {
        return $this->rules;
    }

    abstract public function fire(Channel $channel, Trigger $trigger): void;
}
