<?php

namespace Vigilant\Healthchecks\Tests\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Healthchecks\Actions\AggregateMetrics;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Tests\TestCase;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Models\User;

class AggregateMetricsTest extends TestCase
{
    public function test_it_aggregates_metrics_older_than_the_last_hour(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-01-01 12:00:00'));

        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);

        $healthcheck = Healthcheck::query()->create([
            'team_id' => $team->id,
            'site_id' => null,
            'enabled' => true,
            'domain' => 'example.com',
            'type' => Type::Endpoint->value,
            'token' => 'token',
            'interval' => 5,
        ]);

        $first = Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'cpu',
            'value' => 10,
            'unit' => '%',
            'created_at' => Carbon::parse('2025-01-01 10:05:00'),
            'updated_at' => Carbon::parse('2025-01-01 10:05:00'),
        ]);

        $second = Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'cpu',
            'value' => 20,
            'unit' => '%',
            'created_at' => Carbon::parse('2025-01-01 10:15:00'),
            'updated_at' => Carbon::parse('2025-01-01 10:15:00'),
        ]);

        $third = Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'cpu',
            'value' => 30,
            'unit' => '%',
            'created_at' => Carbon::parse('2025-01-01 10:45:00'),
            'updated_at' => Carbon::parse('2025-01-01 10:45:00'),
        ]);

        $recentOne = Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'cpu',
            'value' => 60,
            'unit' => '%',
            'created_at' => Carbon::parse('2025-01-01 11:05:00'),
            'updated_at' => Carbon::parse('2025-01-01 11:05:00'),
        ]);

        $recentTwo = Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'cpu',
            'value' => 70,
            'unit' => '%',
            'created_at' => Carbon::parse('2025-01-01 11:20:00'),
            'updated_at' => Carbon::parse('2025-01-01 11:20:00'),
        ]);

        app(AggregateMetrics::class)->handle();

        $this->assertDatabaseCount('healthcheck_metrics', 3);

        $firstRefreshed = $first->fresh();

        $this->assertInstanceOf(Metric::class, $firstRefreshed);
        $this->assertSame('20.00', $firstRefreshed->value);
        $this->assertDatabaseMissing('healthcheck_metrics', ['id' => $second->id]);
        $this->assertDatabaseMissing('healthcheck_metrics', ['id' => $third->id]);
        $this->assertDatabaseHas('healthcheck_metrics', ['id' => $recentOne->id]);
        $this->assertDatabaseHas('healthcheck_metrics', ['id' => $recentTwo->id]);

        $this->assertSame(2, Metric::query()
            ->where('created_at', '>=', Carbon::parse('2025-01-01 11:00:00'))
            ->count());

        Carbon::setTestNow();
    }
}
