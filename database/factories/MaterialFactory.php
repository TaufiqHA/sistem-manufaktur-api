<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        $categories = ['RAW', 'FINISHING', 'HARDWARE'];
        
        return [
            'code' => $this->faker->unique()->lexify('MAT-?????'),
            'name' => $this->faker->words(3, true),
            'unit' => $this->faker->randomElement(['kg', 'pcs', 'liter', 'meter', 'bundle', 'sheet']),
            'current_stock' => $this->faker->numberBetween(0, 1000),
            'safety_stock' => $this->faker->numberBetween(0, 100),
            'price_per_unit' => $this->faker->randomFloat(2, 100, 10000),
            'category' => $this->faker->randomElement($categories),
        ];
    }

    /**
     * Indicate that the material has low stock
     */
    public function lowStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'current_stock' => $this->faker->numberBetween(0, 9),
                'safety_stock' => $this->faker->numberBetween(10, 50),
            ];
        });
    }

    /**
     * Indicate that the material has sufficient stock
     */
    public function sufficientStock(): static
    {
        return $this->state(function (array $attributes) {
            $safetyStock = $this->faker->numberBetween(5, 50);
            return [
                'current_stock' => $this->faker->numberBetween($safetyStock + 1, 1000),
                'safety_stock' => $safetyStock,
            ];
        });
    }

    /**
     * Set a specific category
     */
    public function category(string $category): static
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category' => $category,
            ];
        });
    }
}