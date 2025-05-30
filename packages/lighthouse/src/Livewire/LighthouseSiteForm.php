<?php

namespace Vigilant\Lighthouse\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseSiteForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public Forms\LighthouseSiteForm $form;

    #[Locked]
    public LighthouseMonitor $lighthouseMonitor;

    public function mount(?LighthouseMonitor $monitor): void
    {
        if ($monitor !== null) {
            if ($monitor->exists) {
                $this->authorize('update', $monitor);
            } else {
                $this->authorize('create', $monitor);
                /** @var int $defaultInterval */
                $defaultInterval = collect(config('lighthouse.intervals'))->keys()->first() ?? 60 * 24; // @phpstan-ignore-line
                $this->form->interval = $defaultInterval;
            }

            $this->form->fill($monitor->toArray());
            $this->lighthouseMonitor = $monitor;
        }
    }

    #[On('save')]
    public function save(): void
    {
        $this->validate();

        if ($this->lighthouseMonitor->exists) {
            $this->authorize('update', $this->lighthouseMonitor);

            $this->lighthouseMonitor->update($this->form->all());
        } else {
            $this->authorize('create', $this->lighthouseMonitor);

            $this->lighthouseMonitor = LighthouseMonitor::query()->create(
                $this->form->all()
            );
        }

        if (! $this->inline) {
            $this->alert(
                __('Saved'),
                __('Lighthouse monitor was successfully :action',
                    ['action' => $this->lighthouseMonitor->wasRecentlyCreated ? 'created' : 'saved']),
                AlertType::Success
            );
            $this->redirectRoute('lighthouse.index', ['monitor' => $this->lighthouseMonitor]);
        }
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'lighthouse::livewire.lighthouse-site-form';

        return view($view, [
            'updating' => $this->lighthouseMonitor->exists,
        ]);
    }
}
