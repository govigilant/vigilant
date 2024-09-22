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
            $this->form->fill($crawler->toArray());
        } else {
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

        $this->validate();

        if ($this->crawler->exists) {
            $this->crawler->update($this->form->all());
        } else {
            $this->crawler = Crawler::query()->create(
                $this->form->all()
            );
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
        return view('crawler::livewire.crawler-form', [
            'updating' => $this->crawler->exists,
            'invalidDay' => ($this->form->settings['scheduleConfig']['monthDay'] ?? 0) > 28,
        ]);
    }
}
