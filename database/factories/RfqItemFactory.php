<?php

namespace Database\Factories;

use App\Models\Rfq;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RfqItem>
 */
class RfqItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rfq_id' => Rfq::factory(),
            'material_id' => Material::factory(),
            'name' => $this->faker->word,
            'qty' => $this->faker->numberBetween(1, 100),
        ];
    }
}
