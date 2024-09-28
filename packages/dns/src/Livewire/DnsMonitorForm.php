<?php

namespace Vigilant\Dns\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Dns\Actions\ResolveRecord;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;

class DnsMonitorForm extends Component
{
    use DisplaysAlerts;

    public Forms\DnsMonitorForm $form;

    public bool $resolveFailed = false;

    #[Locked]
    public DnsMonitor $dnsMonitor;

    public function mount(?DnsMonitor $monitor): void
    {
        if ($monitor !== null) {
            if ($monitor->exists) {
                $this->authorize('edit', $monitor);
            } else {
                $this->authorize('create', $monitor);
            }

            $this->form->fill($monitor->toArray());
            $this->dnsMonitor = $monitor;
        }
    }

    public function resolve(): void
    {
        $this->validate([
            'form.record' => 'required',
            'form.type' => 'required',
        ]);

        /** @var ResolveRecord $resolver */
        $resolver = app(ResolveRecord::class);

        $result = $resolver->resolve($this->form->type, $this->form->record);

        if ($result !== null) {
            $this->form->value = $result;
            $this->resolveFailed = false;
        } else {
            $this->resolveFailed = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        if ($this->dnsMonitor->exists) {
            $this->authorize('update', $this->dnsMonitor);

            $this->dnsMonitor->update($this->form->all());
        } else {
            $this->authorize('create', $this->dnsMonitor);

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
