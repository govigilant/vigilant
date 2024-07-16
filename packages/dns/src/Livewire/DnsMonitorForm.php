<?php

namespace Vigilant\Dns\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;

class DnsMonitorForm extends Component
{
    use DisplaysAlerts;

    public Forms\DnsMonitorForm $form;

    #[Locked]
    public DnsMonitor $dnsMonitor;

    public function mount(?DnsMonitor $monitor): void
    {
        if ($monitor !== null) {
            $this->form->fill($monitor->toArray());
            $this->dnsMonitor = $monitor;
        }
    }

    public function resolve(): void
    {
        $this->form->value = '127.0.0.1';
    }

    public function save(): void
    {
        $this->validate();

        if ($this->dnsMonitor->exists) {
            $this->dnsMonitor->update($this->form->all());
        } else {
            $this->dnsMonitor = DnsMonitor::query()->create(
                $this->form->all()
            );
        }

        $this->alert(
            __('Saved'),
            __('DNS monitor was successfully :action',
                ['action' => $this->dnsMonitor->wasRecentlyCreated ? 'created' : 'saved']),
            AlertType::Success
        );
        $this->redirectRoute('dns.index');
    }

    public function render(): mixed
    {
        return view('dns::livewire.dns-monitor-form', [
            'updating' => $this->dnsMonitor->exists,
        ]);
    }
}
