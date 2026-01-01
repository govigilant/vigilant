<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Sites\Http\Livewire\Forms\CreateSiteForm;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Models\Monitor as UptimeMonitor;

class SiteForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public CreateSiteForm $form;

    #[Locked]
    public Site $site;

    public function mount(?Site $site): void
    {
        if ($site !== null) {
            $this->form->fill($site->toArray());
            $this->site = $site;
        }
    }

    #[On('save')]
    public function save(): void
    {
        // Save tabs
        if ($this->site->exists) {
            $this->dispatch('save');
        }

        $this->form->url = $this->normalizeUrl($this->form->url);

        $this->validate();

        if ($this->site->exists) {
            $this->site->update($this->form->all());
        } else {
            $this->site = Site::query()->create(
                $this->form->all()
            );
        }

        if ($this->inline) {
            $this->dispatch('siteSaved', $this->site->id);

            return;
        }

        $this->alert(
            __('Saved'),
            __('Site was successfully :action', ['action' => $this->site->wasRecentlyCreated ? 'created' : 'saved']),
            AlertType::Success
        );

        $this->redirectRoute('site.view', ['site' => $this->site]);
    }

    public function render(): View
    {
        $tabs = collect($this->tabs())
            ->filter(fn (array $tab) => ! array_key_exists('gate', $tab) || Gate::check($tab['gate']))
            ->toArray();

        /** @var view-string $view */
        $view = 'sites::livewire.form';

        return view($view, [
            'updating' => $this->site->exists,
            'tabs' => $tabs,
        ]);
    }

    private function normalizeUrl(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return rtrim($url, '/');
        }

        return sprintf('%s://%s', $parts['scheme'], $parts['host']);
    }

    /** @return array<string, array<string, string>> */
    protected function tabs(): array
    {
        return [
            'uptime' => [
                'title' => __('Uptime Monitoring'),
                'component' => 'sites.tabs.uptime-monitor',
                'gate' => 'use-uptime',
                'model' => UptimeMonitor::class,
            ],

            'lighthouse' => [
                'title' => __('Lighthouse Monitoring'),
                'component' => 'sites.tabs.lighthouse-monitor',
                'gate' => 'use-lighthouse',
                'model' => LighthouseMonitor::class,
            ],

            'dns' => [
                'title' => __('DNS Monitoring'),
                'component' => 'sites.tabs.dns-monitors',
                'gate' => 'use-dns',
                'model' => DnsMonitor::class,
            ],

            'crawler' => [
                'title' => __('Link Issues'),
                'component' => 'sites.tabs.crawler',
                'gate' => 'use-crawler',
                'model' => Crawler::class,
            ],

            'certificates' => [
                'title' => __('Certificate Monitoring'),
                'component' => 'sites.tabs.certificate-monitor',
                'gate' => 'use-certificates',
                'model' => CertificateMonitor::class,
            ],

            'healthcheck' => [
                'title' => __('Healthcheck Monitoring'),
                'component' => 'sites.tabs.healthcheck-monitor',
                'gate' => 'use-healthchecks',
                'model' => Healthcheck::class,
            ],
        ];
    }
}
