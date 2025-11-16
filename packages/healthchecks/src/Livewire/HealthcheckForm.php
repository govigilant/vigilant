<?php

namespace Vigilant\Healthchecks\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Healthchecks\Livewire\Forms\HealthcheckForm as HealthcheckFormObject;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckForm extends Component
{
    use DisplaysAlerts;

    public HealthcheckFormObject $form;

    #[Locked]
    public Healthcheck $healthcheck;

    public function mount(?Healthcheck $healthcheck): void
    {
        if ($healthcheck !== null) {
            $this->form->fill($healthcheck->except('type'));
            $this->healthcheck = $healthcheck;
            if ($healthcheck->exists) {
                $this->authorize('update', $healthcheck);
                $this->form->type = $healthcheck->type;
            } else {
                $this->authorize('create', $healthcheck);
                /** @var array<int, string> $intervals */
                $intervals = config('healthchecks.intervals', []);
                /** @var int $defaultInterval */
                $defaultInterval = collect($intervals)->keys()->first() ?? 60;
                $this->form->interval = $defaultInterval;
            }

        }
    }

    public function save(): void
    {
        $this->validate();

        $isNew = ! $this->healthcheck->exists;

        if ($this->healthcheck->exists) {
            $this->authorize('update', $this->healthcheck);

            $this->healthcheck->update($this->form->all());
        } else {
            $this->authorize('create', $this->healthcheck);

            $this->healthcheck = Healthcheck::query()->create(
                $this->form->all()
            );
        }

        $this->alert(
            __('Saved'),
            __('Healthcheck was successfully :action',
                ['action' => $this->healthcheck->wasRecentlyCreated ? 'created' : 'saved']),
            AlertType::Success
        );

        if ($isNew) {
            $this->redirectRoute('healthchecks.setup', ['healthcheck' => $this->healthcheck, 'new' => 1]);
        } else {
            $this->redirectRoute('healthchecks.index');
        }
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'healthchecks::livewire.healthcheck-form';

        return view($view, [
            'updating' => $this->healthcheck->exists,
        ]);
    }
}
