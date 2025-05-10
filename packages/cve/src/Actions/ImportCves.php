<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Vigilant\Cve\Models\Cve;

class ImportCves
{
    public function __construct(
        protected ImportCve $importCve,
    ) {}

    public function import(?Carbon $from = null): void
    {
        if ($from === null) {
            $from = Cve::query()
                ->orderBy('published_at', 'desc')
                ->first()->published_at ?? null;
        }
        if ($from === null) {
            $from = now()->subDay();
        }

        $endpoint = 'https://services.nvd.nist.gov/rest/json/cves/2.0';

        $to = $from->clone();
        $to->addDays(30);

        if ($to->isFuture()) {
            $to = now();
        }

        $response = Http::get($endpoint, [
            'lastModStartDate' => $from->format('Y-m-d\TH:i:s\Z'),
            'lastModEndDate' => $to->format('Y-m-d\TH:i:s\Z'),
        ])->throw();

        $cves = $response->json('vulnerabilities', []);

        foreach ($cves as $cve) {
            $this->importCve->import($cve);
        }
    }
}
