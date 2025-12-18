<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $deadline = $this->faker->dateTimeBetween($startDate, '+6 months');

        return [
            'code' => $this->faker->unique()->bothify('PROJ-?????'),
            'name' => $this->faker->sentence(3, true),
            'customer' => $this->faker->company(),
            'start_date' => $startDate,
            'deadline' => $deadline,
            'status' => $this->faker->randomElement(['PLANNED', 'IN_PROGRESS', 'COMPLETED', 'ON_HOLD', 'CANCELLED']),
            'progress' => $this->faker->numberBetween(0, 100),
            'qty_per_unit' => $this->faker->numberBetween(1, 1000),
            'procurement_qty' => $this->faker->numberBetween(1, 5000),
            'total_qty' => $this->faker->numberBetween(1, 10000),
            'unit' => $this->faker->randomElement(['PCS', 'SET', 'UNIT', 'KIT', 'METER', 'KG']),
            'is_locked' => $this->faker->boolean(10), // 10% chance of being locked
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
