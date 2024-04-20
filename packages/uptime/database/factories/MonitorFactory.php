<?php

namespace Vigilant\Uptime\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;

class MonitorFactory extends Factory
{
    protected $model = Monitor::class;

    public function definition(): array
    {
        return [
            'team_id' => 1,
            'name' => 'Monitor',
            'type' => Type::Http,
            'settings' => [
                'host' => '1.1.1.1',
            ],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
        ];
    }
}
