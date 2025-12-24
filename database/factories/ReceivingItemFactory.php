<?php

namespace Database\Factories;

use App\Models\ReceivingGood;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReceivingItem>
 */
class ReceivingItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'receiving_id' => ReceivingGood::factory(),
            'material_id' => Material::factory(),
            'name' => $this->faker->word,
            'qty' => $this->faker->numberBetween(1, 100),
        ];
    }
}
