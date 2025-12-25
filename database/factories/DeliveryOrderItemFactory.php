<?php

namespace Database\Factories;

use App\Models\DeliveryOrder;
use App\Models\FinishedGoodsWarehouse;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryOrderItem>
 */
class DeliveryOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_order_id' => function () {
                return \App\Models\DeliveryOrder::factory()->create()->id;
            },
            'warehouse_id' => function () {
                return \App\Models\FinishedGoodsWarehouse::factory()->create()->id;
            },
            'project_id' => function () {
                return \App\Models\Project::factory()->create()->id;
            },
            'project_name' => fake()->sentence(3),
            'item_name' => fake()->word(),
            'qty' => fake()->numberBetween(1, 100),
            'unit' => fake()->randomElement(['pcs', 'unit', 'box', 'pack', 'set', 'roll', 'meter', 'kg']),
        ];
    }
}
