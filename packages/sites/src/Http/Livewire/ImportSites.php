<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Sites\Jobs\ImportSitesJob;
use Vigilant\Users\Models\User;

class ImportSites extends Component
{
    use DisplaysAlerts;

    public string $urls = '';

    public array $validatedDomains = [];

    public array $monitors = [];

    public function confirm(): void
    {
        $this->validatedDomains = str($this->urls)
            ->explode("\n")
            ->map(fn ($url) => trim($url))
            ->filter(fn ($url) => ! blank($url))
            ->filter(fn ($url) => preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,}$/', $url))
            ->all();

        $this->urls = implode(PHP_EOL, $this->validatedDomains);

        $this->validate([
            'urls' => 'required|string',
            'monitors' => 'array|min:1',
        ]);
    }

    public function cancel(): void
    {
        $this->validatedDomains = [];
    }

    public function import(): void
    {
        /** @var User $user */
        $user = auth()->user();

        foreach ($this->validatedDomains as $domain) {
            ImportSitesJob::dispatch(
                teamId: $user->current_team_id,
                domain: $domain,
                monitors: $this->monitors,
            );
        }

        $this->alert(
            __('Saved'),
            __(':count sites are being imported', ['count' => count($this->validatedDomains)]),
            AlertType::Success
        );

        $this->redirectRoute('sites');
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'sites::livewire.import';

        return view($view, [
            'availableMonitors' => [
                'uptime' => __('Uptime'),
                'lighthouse' => __('Lighthouse'),
                'dns' => __('DNS'),
                'certificate' => __('Certificate'),
                'crawler' => __('Link Issues'),
            ],
        ]);
    }
}
