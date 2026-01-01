<?php

namespace Vigilant\Certificates\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;

class CertificateMonitorForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public Forms\CertificateMonitorForm $form;

    #[Locked]
    public CertificateMonitor $certificateMonitor;

    public function mount(?CertificateMonitor $monitor): void
    {
        if ($monitor !== null) {
            if ($monitor->exists) {
                $this->authorize('update', $monitor);
            } else {
                $this->authorize('create', $monitor);
            }

            $this->form->fill($monitor->toArray());
            $this->certificateMonitor = $monitor;
        }
    }

    #[On('save')]
    public function save(): void
    {
        $this->validate();

        if ($this->certificateMonitor->exists) {
            $this->authorize('update', $this->certificateMonitor);

            $this->certificateMonitor->update($this->form->all());
        } else {
            $this->authorize('create', $this->certificateMonitor);

            $this->certificateMonitor = CertificateMonitor::query()->create(
                $this->form->all()
            );
        }

        if (! $this->inline) {
            $this->alert(
                __('Saved'),
                __('Certificate monitor was successfully :action',
                    ['action' => $this->certificateMonitor->wasRecentlyCreated ? 'created' : 'saved']),
                AlertType::Success
            );
            $this->redirectRoute('certificates.index', ['monitor' => $this->certificateMonitor]);
        }
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'certificates::livewire.certificate-monitor-form';

        return view($view, [
            'updating' => $this->certificateMonitor->exists,
        ]);
    }

}
