<?php

namespace Vigilant\Cve\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Actions\ImportAllCves;
use Vigilant\Cve\Jobs\ImportAllCvesJob;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Tests\TestCase;

class ImportAllCvesTest extends TestCase
{
    #[Test]
    public function it_imports_full_page_of_cves(): void
    {
        Bus::fake();

        $vulnerabilities = [];
        for ($i = 0; $i < 500; $i++) {
            $vulnerabilities[] = [
                'cve' => [
                    'id' => "CVE-2024-{$i}",
                    'descriptions' => [
                        ['lang' => 'en', 'value' => "Vulnerability {$i}"],
                    ],
                    'metrics' => [
                        'cvssMetricV31' => [
                            ['cvssData' => ['baseScore' => 5.0]],
                        ],
                    ],
                    'published' => '2024-01-01T00:00:00Z',
                    'lastModified' => '2024-01-02T00:00:00Z',
                ],
            ];
        }

        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => $vulnerabilities,
            ]),
        ])->preventStrayRequests();

        /** @var ImportAllCves $action */
        $action = app(ImportAllCves::class);
        $action->import(0);

        // Should import all 500 CVEs
        $this->assertEquals(500, Cve::query()->count());

        // Should dispatch next page job
        Bus::assertDispatched(ImportAllCvesJob::class);
    }

    #[Test]
    public function it_does_not_dispatch_next_job_for_partial_page(): void
    {
        Bus::fake();

        $vulnerabilities = [];
        for ($i = 0; $i < 250; $i++) {
            $vulnerabilities[] = [
                'cve' => [
                    'id' => "CVE-2024-{$i}",
                    'descriptions' => [
                        ['lang' => 'en', 'value' => "Vulnerability {$i}"],
                    ],
                    'metrics' => [],
                    'published' => '2024-01-01T00:00:00Z',
                    'lastModified' => '2024-01-02T00:00:00Z',
                ],
            ];
        }

        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => $vulnerabilities,
            ]),
        ])->preventStrayRequests();

        /** @var ImportAllCves $action */
        $action = app(ImportAllCves::class);
        $action->import(0);

        // Should import all 250 CVEs
        $this->assertEquals(250, Cve::query()->count());

        // Should NOT dispatch next page job (less than 500)
        Bus::assertNotDispatched(ImportAllCvesJob::class);
    }

    #[Test]
    public function it_uses_correct_pagination_parameters(): void
    {
        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [],
            ]),
        ])->preventStrayRequests();

        /** @var ImportAllCves $action */
        $action = app(ImportAllCves::class);
        $action->import(3);

        Http::assertSent(function ($request) {
            $url = $request->url();

            return str_contains($url, 'resultsPerPage=500') &&
                   str_contains($url, 'startIndex=1500'); // 3 * 500
        });
    }

    #[Test]
    public function it_does_not_notify_for_bulk_import(): void
    {
        Bus::fake();

        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [
                    [
                        'cve' => [
                            'id' => 'CVE-2024-NEW',
                            'descriptions' => [
                                ['lang' => 'en', 'value' => 'Recent vulnerability'],
                            ],
                            'metrics' => [
                                'cvssMetricV31' => [
                                    ['cvssData' => ['baseScore' => 9.0]],
                                ],
                            ],
                            'published' => now()->subDays(2)->toIso8601String(),
                            'lastModified' => now()->toIso8601String(),
                        ],
                    ],
                ],
            ]),
        ])->preventStrayRequests();

        /** @var ImportAllCves $action */
        $action = app(ImportAllCves::class);
        $action->import(0);

        // Should not dispatch match job even for recent CVE
        Bus::assertNotDispatched(\Vigilant\Cve\Jobs\MatchCveMonitorsJob::class);
    }

    #[Test]
    public function it_handles_empty_response(): void
    {
        Bus::fake();

        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [],
            ]),
        ])->preventStrayRequests();

        /** @var ImportAllCves $action */
        $action = app(ImportAllCves::class);
        $action->import(0);

        $this->assertEquals(0, Cve::query()->count());
        Bus::assertNotDispatched(ImportAllCvesJob::class);
    }
}
