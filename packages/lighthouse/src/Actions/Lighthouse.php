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
        $output = Process::run('lighthouse '.$site->url.' --output json --quiet --chrome-flags="--headless --no-sandbox"')
            ->throw()
            ->output();

        $result = json_decode($output, true);

        file_put_contents(base_path('lighthouse.json'), $output);

        /** @var array<string, array> $categoriesResult */
        $categoriesResult = $result['categories'] ?? [];

        /** @var array<string, array> $audits */
        $audits = $result['audits'];

        $categories = collect($categoriesResult)
            ->mapWithKeys(function (array $result, string $key): array {
                return [str_replace('-', '_', $key) => $result['score']];
            })
            ->toArray();

        /** @var LighthouseResult $result */
        $result = $site->lighthouseResults()->create($categories);

        foreach ($audits as $audit) {
            $result->audits()->create([
                'audit' => $audit['id'],
                'title' => $audit['title'],
                'explanation' => $audit['explanation'] ?? null,
                'description' => $audit['description'] ?? null,
                'score' => $audit['score'] ?? null,
                'scoreDisplayMode' => $audit['scoreDisplayMode'],
                'details' => $audit['details'] ?? null,
                'warnings' => $audit['warnings'] ?? null,
                'items' => $audit['items'] ?? null,
                'metricSavings' => $audit['metricSavings'] ?? null,
                'guidanceLevel' => $audit['guidanceLevel'] ?? null,
                'numericValue' => $audit['numericValue'] ?? null,
                'numericUnit' => $audit['numericUnit'] ?? null,
            ]);
        }

        $this->lighthouseResult->check($result);
    }
}
