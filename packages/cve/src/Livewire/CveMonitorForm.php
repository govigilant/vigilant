<?php

namespace Vigilant\Cve\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;

class CveMonitorForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public Forms\CveMonitorForm $form;

    #[Locked]
    public CveMonitor $cveMonitor;

    public function mount(?CveMonitor $monitor): void
    {
        if ($monitor !== null) {
            if ($monitor->exists) {
                $this->authorize('update', $monitor);
            } else {
                $this->authorize('create', $monitor);
            }

            $this->form->fill($monitor->toArray());
            $this->cveMonitor = $monitor;
        }
    }

    public function save(): void
    {
        $this->validate();

        if ($this->cveMonitor->exists) {
            $this->authorize('update', $this->cveMonitor);

            $this->cveMonitor->update($this->form->all());
        } else {
            $this->authorize('create', $this->cveMonitor);

            $this->cveMonitor = CveMonitor::query()->create(
                $this->form->all()
            );
        }

        $this->alert(
            __('Saved'),
            __('CVE monitor was successfully :action',
                ['action' => $this->cveMonitor->wasRecentlyCreated ? 'created' : 'saved']),
            AlertType::Success
        );
        $this->redirectRoute('cve.index');
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'cve::livewire.cve-monitor-form';

        return view($view, [
            'updating' => $this->cveMonitor->exists,
        ]);
    }
}
