<?php

namespace Database\Factories;

use App\Models\FinishedGoodsWarehouse;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\FinishedGoodsWarehouse>
 */
class FinishedGoodsWarehouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\FinishedGoodsWarehouse>
     */
    protected $model = FinishedGoodsWarehouse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random project ID or create one if none exists
        $projectId = Project::inRandomOrder()->first()?->id ?? Project::factory()->create()->id;
        
        $totalProduced = $this->faker->numberBetween(10, 1000);
        $shippedQty = $this->faker->numberBetween(0, $totalProduced);
        $availableStock = $totalProduced - $shippedQty;

        return [
            'project_id' => $projectId,
            'item_name' => $this->faker->word . ' ' . $this->faker->word,
            'total_produced' => $totalProduced,
            'shipped_qty' => $shippedQty,
            'available_stock' => $availableStock,
            'unit' => $this->faker->randomElement(['pcs', 'kg', 'meter', 'liter', 'unit']),
            'status' => $this->faker->randomElement(['not validate', 'validated']),
        ];
    }
}