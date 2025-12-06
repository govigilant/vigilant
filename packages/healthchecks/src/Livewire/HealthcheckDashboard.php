<?php

namespace Vigilant\Healthchecks\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckDashboard extends Component
{
    #[Locked]
    public int $healthcheckId;

    public function mount(int $healthcheckId): void
    {
        $this->healthcheckId = $healthcheckId;
    }

    public function render(): View
    {
        $healthcheck = Healthcheck::query()->findOrFail($this->healthcheckId);

        /** @var view-string $view */
        $view = 'healthchecks::livewire.healthcheck-dashboard';

        return view($view, [
            'healthcheck' => $healthcheck,
        ]);
    }
}
