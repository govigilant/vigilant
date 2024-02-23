<?php

namespace Vigilant\Uptime\Http\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Uptime\Http\Livewire\Forms\CreateUptimeMonitorForm;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorForm extends Component
{
    public CreateUptimeMonitorForm $form;

    #[Locked]
    public Monitor $monitor;

    public function mount(?Monitor $monitor)
    {
        $this->form->fill($monitor->toArray());
        $this->monitor = $monitor;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->monitor->exists) {
            $this->monitor->update($this->form->all());
        } else {
            Monitor::query()->create(
                $this->form->all()
            );
        }

        $this->redirectRoute('uptime');
    }

    public function render(): View
    {
        return view('uptime::livewire.monitor.form', [
            'updating' => $this->monitor->exists
        ]);
    }
}
