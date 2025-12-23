<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rfq>
 */
class RfqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('RFQ-????-#####'),
            'date' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'description' => $this->faker->optional()->text(200),
            'status' => $this->faker->randomElement(['DRAFT', 'PO_CREATED']),
        ];
    }
}
