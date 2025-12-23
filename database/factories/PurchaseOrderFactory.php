<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('PO-#####'),
            'rfq_id' => Rfq::factory(),
            'supplier_id' => Supplier::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'description' => $this->faker->optional()->sentence,
            'grand_total' => $this->faker->randomFloat(2, 100, 10000),
            'status' => $this->faker->randomElement(['OPEN', 'RECEIVED']),
        ];
    }
}