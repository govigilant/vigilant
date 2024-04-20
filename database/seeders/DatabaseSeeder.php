<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Vigilant\Uptime\Models\Monitor;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var Monitor $monitor */
        $monitor = Monitor::factory()->create();

        $latencyMin = 5;
        $latencyMax = 20;

        $currentDate = now();

        for ($i = 0; $i < 72; $i++) {

            $currentDate->subHour();

            $monitor->aggregatedResults()->create([
                'total_time' => rand($latencyMin, $latencyMax),
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
            ]);

        }

    }
}
