<?php

namespace Vigilant\Cve\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Actions\ImportCve;
use Vigilant\Cve\Jobs\MatchCveMonitorsJob;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Tests\TestCase;

class ImportCveTest extends TestCase
{
    #[Test]
    public function it_imports_cve_with_english_description(): void
    {
        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-1234',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Test vulnerability description'],
                    ['lang' => 'es', 'value' => 'Descripción de vulnerabilidad'],
                ],
                'metrics' => [
                    'cvssMetricV31' => [
                        ['cvssData' => ['baseScore' => 7.5]],
                    ],
                ],
                'published' => '2024-01-01T00:00:00Z',
                'lastModified' => '2024-01-02T00:00:00Z',
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData);

        $this->assertDatabaseHas('cves', [
            'identifier' => 'CVE-2024-1234',
            'description' => 'Test vulnerability description',
            'score' => 7.5,
        ]);
    }

    #[Test]
    public function it_imports_cve_without_english_description(): void
    {
        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-5678',
                'descriptions' => [
                    ['lang' => 'es', 'value' => 'Primera descripción'],
                ],
                'metrics' => [
                    'cvssMetricV2' => [
                        ['cvssData' => ['baseScore' => 5.0]],
                    ],
                ],
                'published' => '2024-01-01T00:00:00Z',
                'lastModified' => '2024-01-02T00:00:00Z',
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData);

        $this->assertDatabaseHas('cves', [
            'identifier' => 'CVE-2024-5678',
            'description' => 'Primera descripción',
            'score' => 5.0,
        ]);
    }

    #[Test]
    public function it_uses_cvss_v2_when_v3_not_available(): void
    {
        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-9999',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Test description'],
                ],
                'metrics' => [
                    'cvssMetricV2' => [
                        ['cvssData' => ['baseScore' => 4.3]],
                    ],
                ],
                'published' => '2024-01-01T00:00:00Z',
                'lastModified' => '2024-01-02T00:00:00Z',
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData);

        $cve = Cve::query()->where('identifier', 'CVE-2024-9999')->first();
        $this->assertNotNull($cve);
        $this->assertEquals(4.3, $cve->score);
    }

    #[Test]
    public function it_updates_existing_cve(): void
    {
        Cve::query()->create([
            'identifier' => 'CVE-2024-1111',
            'description' => 'Old description',
            'score' => 1.0,
            'published_at' => '2024-01-01',
            'modified_at' => '2024-01-01',
            'data' => [],
        ]);

        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-1111',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Updated description'],
                ],
                'metrics' => [
                    'cvssMetricV31' => [
                        ['cvssData' => ['baseScore' => 9.0]],
                    ],
                ],
                'published' => '2024-01-01T00:00:00Z',
                'lastModified' => '2024-01-05T00:00:00Z',
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData);

        $cve = Cve::query()->where('identifier', 'CVE-2024-1111')->first();
        $this->assertNotNull($cve);
        $this->assertEquals('Updated description', $cve->description);
        $this->assertEquals(9.0, $cve->score);
    }

    #[Test]
    public function it_dispatches_match_job_for_recent_cves(): void
    {
        Bus::fake();

        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-NEW',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Recent vulnerability'],
                ],
                'metrics' => [
                    'cvssMetricV31' => [
                        ['cvssData' => ['baseScore' => 8.0]],
                    ],
                ],
                'published' => now()->subDays(2)->toIso8601String(),
                'lastModified' => now()->toIso8601String(),
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData, notify: true);

        Bus::assertDispatched(MatchCveMonitorsJob::class);
    }

    #[Test]
    public function it_does_not_dispatch_match_job_for_old_cves(): void
    {
        Bus::fake();

        $cveData = [
            'cve' => [
                'id' => 'CVE-2020-OLD',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Old vulnerability'],
                ],
                'metrics' => [
                    'cvssMetricV31' => [
                        ['cvssData' => ['baseScore' => 8.0]],
                    ],
                ],
                'published' => now()->subMonths(6)->toIso8601String(),
                'lastModified' => now()->toIso8601String(),
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData, notify: true);

        Bus::assertNotDispatched(MatchCveMonitorsJob::class);
    }

    #[Test]
    public function it_does_not_dispatch_match_job_when_notify_is_false(): void
    {
        Bus::fake();

        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-NONOTIFY',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Recent vulnerability'],
                ],
                'metrics' => [
                    'cvssMetricV31' => [
                        ['cvssData' => ['baseScore' => 8.0]],
                    ],
                ],
                'published' => now()->subDays(2)->toIso8601String(),
                'lastModified' => now()->toIso8601String(),
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData, notify: false);

        Bus::assertNotDispatched(MatchCveMonitorsJob::class);
    }

    #[Test]
    public function it_handles_missing_score(): void
    {
        $cveData = [
            'cve' => [
                'id' => 'CVE-2024-NOSCORE',
                'descriptions' => [
                    ['lang' => 'en', 'value' => 'Vulnerability without score'],
                ],
                'metrics' => [],
                'published' => '2024-01-01T00:00:00Z',
                'lastModified' => '2024-01-02T00:00:00Z',
            ],
        ];

        /** @var ImportCve $action */
        $action = app(ImportCve::class);
        $action->import($cveData);

        $cve = Cve::query()->where('identifier', 'CVE-2024-NOSCORE')->first();
        $this->assertNotNull($cve);
        $this->assertNull($cve->score);
    }
}
