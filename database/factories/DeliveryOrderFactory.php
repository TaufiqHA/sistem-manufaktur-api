<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryOrder>
 */
class DeliveryOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('DO-????-#####'),
            'date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'customer' => $this->faker->company(),
            'address' => $this->faker->address(),
            'driver_name' => $this->faker->name(),
            'vehicle_plate' => $this->faker->bothify('???-####'),
        ];
    }
}
