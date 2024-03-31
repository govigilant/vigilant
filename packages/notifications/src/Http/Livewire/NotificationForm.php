<?php

namespace Vigilant\Notifications\Http\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Notifications\Http\Livewire\Forms\CreateNotificationForm;
use Vigilant\Notifications\Models\Trigger;

class NotificationForm extends Component
{
    public CreateNotificationForm $form;

    public array $channels = [];

    #[Locked]
    public Trigger $trigger;

    public function mount(?Trigger $trigger): void
    {
        $this->trigger = $trigger;
        $this->form->fill($trigger->toArray());
        if ($trigger->exists) {
            $this->channels = $trigger->channels->pluck('id')->toArray();
        }
    }

    public function save(): void
    {
        $this->validate();

        if ($this->trigger->exists) {
            $this->trigger->update($this->form->all());
        } else {
            $this->trigger = Trigger::query()->create(
                $this->form->all()
            );
        }

        $this->trigger->channels()->sync($this->channels);

        $this->redirectRoute('notifications.trigger.edit', ['trigger' => $this->trigger]);
    }

    public function render()
    {
        return view('notifications::livewire.notifications.form', [
            'updating' => $this->trigger->exists,
        ]);
    }
}
