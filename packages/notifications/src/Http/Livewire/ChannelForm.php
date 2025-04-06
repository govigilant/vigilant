<?php

namespace Vigilant\Notifications\Http\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Notifications\Http\Livewire\Forms\CreateChannelForm;
use Vigilant\Notifications\Jobs\SendNotificationJob;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\TestNotification;

class ChannelForm extends Component
{
    use DisplaysAlerts;

    public CreateChannelForm $form;

    #[Locked]
    public ?string $settingsComponent = null;

    #[Locked]
    public bool $componentValidated = false;

    #[Locked]
    public Channel $channelModel;

    public bool $testSent = false;

    public function mount(?Channel $channel): void
    {
        if ($channel !== null) {
            $this->channelModel = $channel;
            $this->form->fill($channel->toArray());

            if ($channel->channel !== null) {
                $this->settingsComponent = $channel->channel::$component ?? null;
            }
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
            $this->redirectRoute('notifications.channel.edit', ['channel' => $this->channelModel]);

            $this->alert(
                __('Saved'),
                __('Channel was successfully :action',
                    ['action' => $this->channelModel->wasRecentlyCreated ? 'created' : 'saved']),
                AlertType::Success
            );
        }
    }

    public function test(): void
    {
        $this->save(false);

        SendNotificationJob::dispatchSync(
            new TestNotification,
            $this->channelModel->team_id,
            $this->channelModel->id
        );

        $this->testSent = true;
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'notifications::livewire.channels.form';

        return view($view, [
            'updating' => $this->channelModel->exists,
        ]);
    }
}
