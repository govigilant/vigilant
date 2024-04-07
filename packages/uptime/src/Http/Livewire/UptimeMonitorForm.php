<?php

namespace Vigilant\Uptime\Http\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Uptime\Http\Livewire\Forms\CreateUptimeMonitorForm;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorForm extends Component
{
    use CanBeInline;

    public CreateUptimeMonitorForm $form;

    #[Locked]
    public Monitor $monitor;

    public function mount(?Monitor $monitor)
    {
        $this->form->fill($monitor->toArray());
        $this->monitor = $monitor;
    }

    #[On('save')]
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

        if (! $this->inline) {
            $this->redirectRoute('uptime');
        }
    }

    public function render(): View
    {
        return view('uptime::livewire.monitor.form', [
            'updating' => $this->monitor->exists,
        ]);
    }
}
