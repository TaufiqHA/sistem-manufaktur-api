<?php

namespace Database\Factories;

use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BomItem>
 */
class BomItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => ProjectItem::factory(), // Using the factory relationship
            'material_id' => Material::factory(), // Using the factory relationship
            'quantity_per_unit' => $this->faker->numberBetween(1, 100),
            'total_required' => $this->faker->numberBetween(1, 1000),
            'allocated' => $this->faker->numberBetween(0, 500),
            'realized' => $this->faker->numberBetween(0, 500),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (BomItem $bomItem) {
            // Any additional configuration after creating the BomItem
            // For example, ensuring that required relationships exist
        });
    }
}