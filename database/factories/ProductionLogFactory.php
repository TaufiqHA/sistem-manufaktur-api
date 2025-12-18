<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionLog>
 */
class ProductionLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shifts = ['SHIFT_1', 'SHIFT_2', 'SHIFT_3'];
        $types = ['OUTPUT', 'DOWNTIME_START', 'DOWNTIME_END'];

        return [
            'step' => fake()->word(),
            'shift' => $shifts[array_rand($shifts)],
            'good_qty' => fake()->numberBetween(0, 1000),
            'defect_qty' => fake()->numberBetween(0, 100),
            'operator' => fake()->name(),
            'logged_at' => fake()->dateTime(),
            'type' => $types[array_rand($types)],
        ];
    }
}
