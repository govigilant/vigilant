<?php

namespace Vigilant\Notifications\Http\Livewire\Channels\Configuration;

use Illuminate\View\View;
use Livewire\Component;
use Vigilant\Notifications\Channels\NotificationChannel;

abstract class ChannelConfiguration extends Component
{
    public string $channel;

    public array $settings = [];

    public array $rules = [];

    public function mount(string $channel, array $settings = []): void
    {
        $this->channel = $channel;
        $this->settings = $settings;
    }

    public function updated(): void
    {
        $this->dispatch('update-channel-validated', false);

        /** @var NotificationChannel $channel */
        $channel = app($this->channel);

        $this->rules = collect($channel->rules())
            ->mapWithKeys(fn (string|array $rules, string $key) => ["settings.$key" => $rules])
            ->toArray();

        $this->validate();

        $this->dispatch('update-channel-settings', $this->settings);
        $this->dispatch('update-channel-validated', true);
    }

    abstract public function render(): View;
}
