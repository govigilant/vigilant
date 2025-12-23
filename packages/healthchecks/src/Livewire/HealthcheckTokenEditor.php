<?php

namespace Vigilant\Healthchecks\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckTokenEditor extends Component
{
    use DisplaysAlerts;

    #[Locked]
    public Healthcheck $healthcheck;

    public string $token = '';

    public function mount(Healthcheck $healthcheck): void
    {
        $this->authorize('view', $healthcheck);
        $this->healthcheck = $healthcheck;
        $this->token = (string) $healthcheck->token;
    }

    protected function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }

    public function save(): void
    {
        if (! $this->healthcheck->type->generatesOwnToken()) {
            return;
        }

        $this->authorize('update', $this->healthcheck);

        $this->validate();

        $this->healthcheck->update([
            'token' => $this->token,
        ]);

        $this->healthcheck->refresh();
        $this->token = (string) $this->healthcheck->token;

        $this->alert(
            __('Saved'),
            __('Token updated successfully.'),
            AlertType::Success
        );
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'healthchecks::livewire.healthcheck-token-editor';

        return view($view);
    }
}
