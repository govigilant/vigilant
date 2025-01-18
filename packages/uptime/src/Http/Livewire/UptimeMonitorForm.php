<?php

namespace Vigilant\Uptime\Http\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Uptime\Http\Livewire\Forms\CreateUptimeMonitorForm;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public CreateUptimeMonitorForm $form;

    #[Locked]
    public Monitor $monitor;

    public function mount(?Monitor $monitor): void
    {
        if ($monitor !== null) {
            if ($monitor->exists) {
                $this->authorize('update', $monitor);
            } else {
                $this->authorize('create', $monitor);
            }

            $this->form->fill($monitor->toArray());
            $this->monitor = $monitor;
        }
    }

    #[On('save')]
    public function save(): void
    {
        $this->validate();

        if ($this->monitor->exists) {
            $this->authorize('update', $this->monitor);

            $this->monitor->update($this->form->all());
        } else {
            $this->authorize('create', $this->monitor);

            $this->monitor = Monitor::query()->create(
                $this->form->all()
            );
        }

        if (! $this->inline) {
            $this->alert(
                __('Saved'),
                __('Uptime monitor was successfully :action',
                    ['action' => $this->monitor->wasRecentlyCreated ? 'created' : 'saved']),
                AlertType::Success
            );
            $this->redirectRoute('uptime');
        }
    }

    public function render(): mixed
    {
        return view('uptime::livewire.monitor.form', [
            'updating' => $this->monitor->exists,
        ]);
    }
}
