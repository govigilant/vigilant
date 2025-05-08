<?php

namespace Vigilant\Sites\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Vigilant\Sites\Actions\ImportSite;

class ImportSiteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /** @param array<string, bool> $monitors */
    public function __construct(
        public int $teamId,
        public string $domain,
        public array $monitors,
    ) {
        $this->onQueue(config()->string('sites.queue'));
    }

    public function handle(ImportSite $importer): void
    {
        $importer->import(
            teamId: $this->teamId,
            domain: $this->domain,
            monitors: $this->monitors,
        );
    }

    public function uniqueId(): string
    {
        return $this->teamId.'-'.$this->domain;
    }

    /** @return array<int, string> */
    public function tags(): array
    {
        return [
            'team:'.$this->teamId,
            $this->domain,
        ];
    }
}
