<?php

namespace Vigilant\Crawler\Livewire\Crawler;

use Cron\CronExpression;
use Illuminate\Support\Carbon;
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

        $nextRun = Carbon::parse((new CronExpression($crawler->schedule))->getNextRunDate());

        /** @var view-string $view */
        $view = 'crawler::livewire.crawler.dashboard';

        return view($view, [
            'total_url_count' => $crawler->totalUrlCount(),
            'issue_count' => $crawler->issueCount(),
            'nextRun' => $crawler->enabled ? $nextRun->diffForHumans() : __('Crawler disabled'),
        ]);
    }
}
