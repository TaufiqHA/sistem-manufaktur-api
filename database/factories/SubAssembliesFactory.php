<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Material;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\subAssemblies>
 */
class SubAssembliesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => $this->faker->unique()->ean13(),
            'name' => $this->faker->word(),
            'qty_per_parent' => $this->faker->numberBetween(1, 10),
            'material_id' => Material::factory(), // Assuming Material factory exists
            'processes' => json_encode([
                'process_1' => $this->faker->word(),
                'process_2' => $this->faker->word(),
            ]),
            'total_needed' => $this->faker->numberBetween(10, 100),
            'completed_qty' => $this->faker->numberBetween(0, 50),
            'total_produced' => $this->faker->numberBetween(0, 50),
            'consumed_qty' => $this->faker->numberBetween(0, 30),
            'step_stats' => json_encode([
                'step_1' => ['completed' => $this->faker->boolean(), 'progress' => $this->faker->randomElement([0, 25, 50, 75, 100])],
                'step_2' => ['completed' => $this->faker->boolean(), 'progress' => $this->faker->randomElement([0, 25, 50, 75, 100])],
            ]),
            'is_locked' => $this->faker->boolean(),
        ];
    }
}
