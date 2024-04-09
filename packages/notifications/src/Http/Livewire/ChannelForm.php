<?php

namespace Vigilant\Notifications\Http\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Notifications\Http\Livewire\Forms\CreateChannelForm;
use Vigilant\Notifications\Jobs\SendNotificationJob;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\TestNotification;

class ChannelForm extends Component
{
    public CreateChannelForm $form;

    #[Locked]
    public ?string $settingsComponent = null;

    #[Locked]
    public bool $componentValidated = false;

    #[Locked]
    public Channel $channelModel;

    public function mount(?Channel $channel): void
    {
        $this->channelModel = $channel;
        $this->form->fill($channel->toArray());

        if ($channel->channel !== null) {
            $this->settingsComponent = $channel->channel::$component ?? null;
        }
    }

    public function updated(): void
    {
        if (blank($this->form->channel)) {
            return;
        }

        $this->settingsComponent = $this->form->channel::$component ?? null;
    }

    #[On('update-channel-settings')]
    public function updateChannelSettings(array $settings): void
    {
        $this->form->settings = $settings;
    }

    #[On('update-channel-validated')]
    public function updateChannelValidated(bool $validated): void
    {
        $this->componentValidated = $validated;
    }

    public function save(bool $redirect = true): void
    {
        if (! $this->componentValidated && $this->settingsComponent !== null) {
            return;
        }

        $this->validate();

        if ($this->channelModel->exists) {
            $this->channelModel->update($this->form->all());
        } else {
            $this->channelModel = Channel::query()->create(
                $this->form->all()
            );
        }

        if ($redirect) {
            $this->redirectRoute('notifications.channels');
        }
    }

    public function test(): void
    {
        $this->save(false);

        SendNotificationJob::dispatchSync(
            new TestNotification(),
            $this->channelModel
        );
    }

    public function render()
    {
        return view('notifications::livewire.channels.form', [
            'updating' => $this->channelModel->exists,
        ]);
    }
}
