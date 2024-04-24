<?php

namespace Vigilant\Users\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vigilant\Users\Models\Team;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Vigilant\Users\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'user_id' => 1,
            'personal_team' => true,
        ];
    }
}
