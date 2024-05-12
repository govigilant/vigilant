<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Facades\Process;
use Vigilant\Lighthouse\Models\LighthouseSite;

class Lighthouse
{
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

        $site->lighthouseResults()->create($categories);
    }
}
