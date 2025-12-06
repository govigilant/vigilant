<?php

namespace Vigilant\Healthchecks\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckSetup extends Component
{
    #[Locked]
    public Healthcheck $healthcheck;

    public bool $isNew = false;

    public function mount(Healthcheck $healthcheck): void
    {
        $this->authorize('view', $healthcheck);
        $this->healthcheck = $healthcheck;
        $this->isNew = request()->query('new') === '1';
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'healthchecks::livewire.healthcheck-setup';

        return view($view, [
            'isNew' => $this->isNew,
        ]);
    }
}
