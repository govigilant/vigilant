<?php

namespace Vigilant\Cve\Tests\Notifications;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Notifications\CveMatchedNotification;
use Vigilant\Cve\Tests\TestCase;
use Vigilant\Notifications\Enums\Level;

class CveMatchedNotificationTest extends TestCase
{
    #[Test]
    public function it_creates_notification(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-1234',
            'description' => 'Apache HTTP Server vulnerability',
            'score' => 7.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);

        $this->assertInstanceOf(CveMatchedNotification::class, $notification);
        $this->assertEquals($monitor->id, $notification->monitor->id);
        $this->assertEquals($cve->id, $notification->cve->id);
    }

    #[Test]
    public function it_returns_correct_title(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-1234',
            'description' => 'Test vulnerability',
            'score' => 8.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);
        $title = $notification->title();

        $this->assertStringContainsString('CVE-2024-1234', $title);
        $this->assertStringContainsString('8.5', $title);
    }

    #[Test]
    public function it_returns_correct_description(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-5678',
            'description' => 'Apache HTTP Server remote code execution vulnerability',
            'score' => 9.8,
            'published_at' => now()->subDays(2),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);
        $description = $notification->description();

        $this->assertStringContainsString('CVE-2024-5678', $description);
        $this->assertStringContainsString('apache', $description);
        $this->assertStringContainsString('9.8', $description);
        $this->assertStringContainsString('Apache HTTP Server', $description);
    }

    #[Test]
    public function it_returns_critical_level_for_high_score(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-CRITICAL',
            'description' => 'Critical vulnerability',
            'score' => 9.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);

        $this->assertEquals(Level::Critical, $notification->level());
    }

    #[Test]
    public function it_returns_warning_level_for_medium_score(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-WARNING',
            'description' => 'Medium vulnerability',
            'score' => 5.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);

        $this->assertEquals(Level::Warning, $notification->level());
    }

    #[Test]
    public function it_returns_info_level_for_low_score(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-INFO',
            'description' => 'Low vulnerability',
            'score' => 2.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);

        $this->assertEquals(Level::Info, $notification->level());
    }

    #[Test]
    public function it_returns_unique_id(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-UNIQUE',
            'description' => 'Test',
            'score' => 5.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);

        $this->assertEquals($cve->id, $notification->uniqueId());
    }

    #[Test]
    public function it_handles_null_score(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-NULL',
            'description' => 'Test',
            'score' => null,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);

        $title = $notification->title();
        $this->assertStringContainsString('0', $title);

        $this->assertEquals(Level::Info, $notification->level());
    }

    #[Test]
    public function it_truncates_long_descriptions(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        $longDescription = str_repeat('This is a very long vulnerability description. ', 50);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-LONG',
            'description' => $longDescription,
            'score' => 5.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $notification = new CveMatchedNotification($monitor, $cve);
        $description = $notification->description();

        // Should be truncated (check for reasonable length, accounting for extra text)
        $this->assertLessThanOrEqual(700, strlen($description));
        $this->assertStringContainsString('...', $description);
    }
}
