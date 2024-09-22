<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Crawler\Models\Crawler as CrawlerModel;
use Vigilant\Sites\Models\Site;

class Crawler extends Component
{
    #[Locked]
    public int $siteId;

    public bool $enabled = false;

    public function mount(Site $site): void
    {
        $this->siteId = $site->id;
        $this->enabled = $this->crawler()->exists;
    }

    #[Computed]
    public function crawler(): CrawlerModel
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($this->siteId);

        /** @var ?CrawlerModel $crawler */
        $crawler = $site->crawler;

        if ($crawler === null) {
            $crawler = new CrawlerModel([
                'site_id' => $site->id,
                'start_url' => $site->url,
            ]);
        }

        return $crawler;
    }

    public function render(): mixed
    {
        return view('sites::livewire.tabs.crawler', [
            'siteId' => $this->siteId,
        ]);
    }
}
