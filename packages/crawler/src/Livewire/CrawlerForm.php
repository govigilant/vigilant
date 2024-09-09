<?php

namespace Vigilant\Crawler\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;

class CrawlerForm extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    public Forms\CrawlerForm $form;

    #[Locked]
    public Crawler $crawler;

    public function mount(?Crawler $crawler): void
    {
        if ($crawler !== null) {
            $this->form->fill($crawler->toArray());
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
            $this->redirectRoute('crawler.index');
        }

    }

    public function render(): mixed
    {
        return view('crawler::livewire.crawler-form', [
            'updating' => $this->crawler->exists,
        ]);
    }
}
