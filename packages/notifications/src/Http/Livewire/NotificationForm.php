<?php

namespace Vigilant\Notifications\Http\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Notifications\Http\Livewire\Forms\CreateNotificationForm;
use Vigilant\Notifications\Models\Trigger;

class NotificationForm extends Component
{
    use DisplaysAlerts;

    public CreateNotificationForm $form;

    public array $channels = [];

    #[Locked]
    public Trigger $trigger;

    public function mount(?Trigger $trigger): void
    {
        if ($trigger !== null) {
            $this->trigger = $trigger;
            $this->form->fill($trigger->toArray());
            if ($trigger->exists) {
                $this->channels = $trigger->channels->pluck('id')->toArray();
            }
        }
    }

    #[On('conditions-updated')]
    public function conditionsUpdated(array $conditions): void
    {
        $this->form->conditions = $conditions;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->trigger->exists) {
            // Never update the notification because of the linked conditions
            $this->trigger->update($this->form->except(['notification']));
        } else {
            $this->trigger = Trigger::query()->create(
                $this->form->all()
            );
        }

        $this->trigger->channels()->sync($this->channels);

        $this->alert(
            __('Saved'),
            __('Notification was successfully :action', ['action' => $this->trigger->wasRecentlyCreated ? 'created' : 'saved']),
            AlertType::Success
        );

        $this->redirectRoute('notifications.trigger.edit', ['trigger' => $this->trigger]);
    }

    public function render(): mixed
    {
        return view('notifications::livewire.notifications.form', [
            'updating' => $this->trigger->exists,
        ]);
    }
}
