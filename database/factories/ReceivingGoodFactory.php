<?php

namespace Database\Factories;

use App\Models\ReceivingGood;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReceivingGood>
 */
class ReceivingGoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('RG-#####'),
            'po_id' => PurchaseOrder::factory(),
            'supplier_id' => Supplier::factory(),
            'date' => $this->faker->dateTime(),
            'description' => $this->faker->optional()->text(200),
        ];
    }
}