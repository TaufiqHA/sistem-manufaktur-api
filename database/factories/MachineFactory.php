<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $machineTypes = ['POTONG', 'PLONG', 'PRESS', 'LAS', 'WT', 'POWDER', 'QC'];
        $statuses = ['IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME'];

        return [
            'code' => $this->faker->unique()->bothify('MCH-???-####'),
            'name' => $this->faker->company() . ' ' . $this->faker->word(),
            'type' => $this->faker->randomElement($machineTypes),
            'capacity_per_hour' => $this->faker->numberBetween(10, 500),
            'status' => $this->faker->randomElement($statuses),
            'personnel' => json_encode([
                [
                    'id' => $this->faker->uuid(),
                    'name' => $this->faker->name(),
                    'position' => $this->faker->jobTitle(),
                ]
            ]),
            'is_maintenance' => $this->faker->boolean(20), // 20% chance of being in maintenance
        ];
    }
}
