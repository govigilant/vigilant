<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Sites\Jobs\ImportSiteJob;
use Vigilant\Users\Models\User;

class ImportSites extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public string $urls = '';

    /** @var array<int, string> */
    public array $validatedDomains = [];

    /** @var array<string, bool> */
    public array $monitors = [
        'uptime' => true,
        'lighthouse' => true,
        'dns' => true,
        'certificate' => true,
        'crawler' => true,
    ];

    public function mount(): void
    {
        if (session()->has('import_urls')) {
            $this->urls = implode(PHP_EOL, session()->get('import_urls', []));
            session()->forget('import_urls');
        }
    }

    public function confirm(): void
    {
        $this->validatedDomains = str($this->urls)
            ->explode("\n")
            ->map(fn (string $url): string => trim($url))
            ->map(function (string $url): string {
                $url = preg_replace('#^https?://#', '', $url) ?? '';
                $url = preg_replace('#/.*$#', '', $url) ?? '';

                return $url;
            })
            ->filter(fn (string $url): bool => ! blank($url))
            ->filter(fn (string $url): mixed => preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,}$/', $url) === 1)
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

        abort_if($user->current_team_id === null, 403);

        foreach ($this->validatedDomains as $domain) {
            ImportSiteJob::dispatch(
                teamId: $user->current_team_id,
                domain: $domain,
                monitors: $this->monitors,
            );
        }

        if ($this->inline) {
            $this->dispatch('sites-imported');

            return;
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
                'uptime' => [
                    'label' => __('Uptime'),
                    'description' => __('Track uptime and response times, get notified when your site goes down'),
                ],
                'lighthouse' => [
                    'label' => __('Lighthouse'),
                    'description' => __('Monitor Google Lighthouse scores'),
                ],
                'dns' => [
                    'label' => __('DNS'),
                    'description' => __('Track changes in DNS records'),
                ],
                'certificate' => [
                    'label' => __('Certificate'),
                    'description' => __('Monitor SSL certificate expiration and validity'),
                ],
                'crawler' => [
                    'label' => __('Link Issues'),
                    'description' => __('Crawl your site to find broken links and errors'),
                ],
            ],
        ]);
    }
}
