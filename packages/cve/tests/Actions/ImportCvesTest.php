<?php

namespace Vigilant\Cve\Tests\Actions;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Actions\ImportCves;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Tests\TestCase;

class ImportCvesTest extends TestCase
{
    #[Test]
    public function it_imports_cves_from_api(): void
    {
        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [
                    [
                        'cve' => [
                            'id' => 'CVE-2024-0001',
                            'descriptions' => [
                                ['lang' => 'en', 'value' => 'First vulnerability'],
                            ],
                            'metrics' => [
                                'cvssMetricV31' => [
                                    ['cvssData' => ['baseScore' => 7.5]],
                                ],
                            ],
                            'published' => '2024-01-01T00:00:00Z',
                            'lastModified' => '2024-01-02T00:00:00Z',
                        ],
                    ],
                    [
                        'cve' => [
                            'id' => 'CVE-2024-0002',
                            'descriptions' => [
                                ['lang' => 'en', 'value' => 'Second vulnerability'],
                            ],
                            'metrics' => [
                                'cvssMetricV31' => [
                                    ['cvssData' => ['baseScore' => 5.0]],
                                ],
                            ],
                            'published' => '2024-01-02T00:00:00Z',
                            'lastModified' => '2024-01-03T00:00:00Z',
                        ],
                    ],
                ],
            ]),
        ])->preventStrayRequests();

        /** @var ImportCves $action */
        $action = app(ImportCves::class);
        $action->import(now()->subDay());

        $this->assertDatabaseHas('cves', [
            'identifier' => 'CVE-2024-0001',
        ]);

        $this->assertDatabaseHas('cves', [
            'identifier' => 'CVE-2024-0002',
        ]);
    }

    #[Test]
    public function it_uses_latest_cve_date_when_from_is_null(): void
    {
        Cve::query()->create([
            'identifier' => 'CVE-2023-9999',
            'description' => 'Latest existing CVE',
            'score' => 5.0,
            'published_at' => '2023-12-15 00:00:00',
            'modified_at' => '2023-12-15 00:00:00',
            'data' => [],
        ]);

        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [],
            ]),
        ])->preventStrayRequests();

        /** @var ImportCves $action */
        $action = app(ImportCves::class);
        $action->import(null);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'pubStartDate=2023-12-15');
        });
    }

    #[Test]
    public function it_uses_yesterday_when_no_cves_exist(): void
    {
        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [],
            ]),
        ])->preventStrayRequests();

        /** @var ImportCves $action */
        $action = app(ImportCves::class);
        $action->import(null);

        Http::assertSent(function ($request) {
            $yesterday = now()->subDay()->format('Y-m-d');

            return str_contains($request->url(), "pubStartDate={$yesterday}");
        });
    }

    #[Test]
    public function it_limits_date_range_to_30_days(): void
    {
        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [],
            ]),
        ])->preventStrayRequests();

        $from = now()->subDays(60);

        /** @var ImportCves $action */
        $action = app(ImportCves::class);
        $action->import($from);

        Http::assertSent(function ($request) use ($from) {
            $expectedEnd = $from->clone()->addDays(30)->format('Y-m-d');

            return str_contains($request->url(), "pubEndDate={$expectedEnd}");
        });
    }

    #[Test]
    public function it_limits_end_date_to_now_when_in_future(): void
    {
        Http::fake([
            'services.nvd.nist.gov/*' => Http::response([
                'vulnerabilities' => [],
            ]),
        ])->preventStrayRequests();

        $from = now()->subDays(10);

        /** @var ImportCves $action */
        $action = app(ImportCves::class);
        $action->import($from);

        Http::assertSent(function ($request) {
            $today = now()->format('Y-m-d');

            return str_contains($request->url(), "pubEndDate={$today}");
        });
    }
}
