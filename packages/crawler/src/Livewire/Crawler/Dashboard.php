<?php

namespace Vigilant\Crawler\Livewire\Crawler;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Crawler\Models\Crawler;

class Dashboard extends Component
{
    #[Locked]
    public int $crawlerId;

    public function mount(int $crawlerId): void
    {
        $this->crawlerId = $crawlerId;
    }

    public function render(): mixed
    {
        /** @var Crawler $crawler */
        $crawler = Crawler::query()->findOrFail($this->crawlerId);

        return view('crawler::livewire.crawler.dashboard', [
            'total_url_count' => $crawler->totalUrlCount(),
            'issue_count' => $crawler->issueCount(),
        ]);
    }
}
