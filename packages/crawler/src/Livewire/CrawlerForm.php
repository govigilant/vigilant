<?php

namespace Vigilant\Crawler\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Sites\Models\Site;

class CrawlerForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public Forms\CrawlerForm $form;

    #[Locked]
    public Crawler $crawler;

    public function mount(?Crawler $crawler, ?int $siteId = null): void
    {
        if ($crawler !== null && $crawler->exists) {
            $this->authorize('update', $crawler);

            $this->form->fill($crawler->toArray());
            $this->form->url_blacklist = $crawler->settings['url_blacklist'] ?? '';
        } else {
            $this->authorize('create', Crawler::class);

            if ($siteId !== null) {
                /** @var Site $site */
                $site = Site::query()->findOrFail($siteId);

                $this->form->start_url = $site->url;
                $this->form->site_id = $siteId;
            }
        }

        if ($crawler !== null) {
            $this->crawler = $crawler;
        }
    }

    public function addListItem(string $field): void
    {
        if ($field === 'form.sitemaps') {
            $this->form->sitemaps[] = '';
        }
    }

    #[On('save')]
    public function save(): void
    {
        $this->form->sitemaps = $this->form->sitemaps !== null ? array_filter($this->form->sitemaps) : null;
        $this->form->schedule = $this->getCronSchedule();
        $this->form->settings = array_merge($this->form->settings ?? [], [
            'url_blacklist' => $this->form->url_blacklist,
        ]);

        $this->validate();

        $data = collect($this->form->all())->except('url_blacklist')->all();

        if ($this->crawler->exists) {
            $this->authorize('update', $this->crawler);

            $this->crawler->update($data);
        } else {
            $this->authorize('create', $this->crawler);

            $this->crawler = Crawler::query()->create($data);
        }

        if (! $this->inline) {
            $this->alert(
                __('Saved'),
                __('Crawler was successfully :action',
                    ['action' => $this->crawler->wasRecentlyCreated ? 'created' : 'saved']),
                AlertType::Success
            );
            $this->redirectRoute('crawler.view', ['crawler' => $this->crawler]);
        }
    }

    protected function getCronSchedule(): string
    {
        $type = $this->form->settings['scheduleConfig']['type'] ?? 'montly';
        $hour = $this->form->settings['scheduleConfig']['hour'] ?? 0;
        $weekDay = $this->form->settings['scheduleConfig']['weekDay'] ?? 0;
        $monthDay = $this->form->settings['scheduleConfig']['monthDay'] ?? 0;

        return match ($type) {
            'daily' => "0 $hour * * *",
            'weekly' => "0 $hour * * $weekDay",
            default => "0 $hour $monthDay * *",
        };
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'crawler::livewire.crawler-form';

        return view($view, [
            'updating' => $this->crawler->exists,
            'invalidDay' => ($this->form->settings['scheduleConfig']['monthDay'] ?? 0) > 28,
        ]);
    }
}
