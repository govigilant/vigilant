<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Facades\Process;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseSite;

class Lighthouse
{
    public function __construct(protected CheckLighthouseResult $lighthouseResult)
    {
    }

    public function run(LighthouseSite $site): void
    {
        $output = Process::run('lighthouse '.$site->url.' --output json --quiet --chrome-flags="--headless"')
            ->throw()
            ->output();

        $result = json_decode($output, true);

        /** @var array<string, array> $categoriesResult */
        $categoriesResult = $result['categories'] ?? [];

        $categories = collect($categoriesResult)
            ->mapWithKeys(function (array $result, string $key): array {
                return [str_replace('-', '_', $key) => $result['score']];
            })
            ->toArray();

        /** @var LighthouseResult $result */
        $result = $site->lighthouseResults()->create($categories);

        $this->lighthouseResult->check($result);
    }
}
