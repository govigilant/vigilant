<?php

namespace Vigilant\Uptime\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Vigilant\Uptime\Http\Livewire\Forms\CreateUptimeMonitorForm;
use Vigilant\Uptime\Models\Monitor;

class CreateUptimeMonitor extends Component
{
    public CreateUptimeMonitorForm $form;

    public function save(): void
    {
        $this->validate();

        Monitor::query()->create(
            $this->form->all()
        );

        $this->redirectRoute('uptime');
    }

    public function render(): View
    {
        return view('uptime::livewire.create-monitor');
    }
}
