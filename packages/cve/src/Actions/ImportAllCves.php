<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Facades\Http;
use Vigilant\Cve\Jobs\ImportAllCvesJob;

class ImportAllCves
{
    public function __construct(
        protected ImportCve $importCve,
    ) {}

    public function import(int $page): void
    {
        $endpoint = 'https://services.nvd.nist.gov/rest/json/cves/2.0';

        $pageSize = 2000;

        $response = Http::get($endpoint, [
            'resultsPerPage' => $pageSize,
            'startIndex' => $page * $pageSize,
        ])->throw();

        $cves = $response->json('vulnerabilities', []);

        foreach ($cves as $cve) {
            $this->importCve->import($cve, false);
        }

        if (count($cves) === 2000) {
            ImportAllCvesJob::dispatch($page + 1)->delay(now()->addSeconds(10));
        }
    }
}
