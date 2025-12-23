<?php

namespace Database\Factories;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PoItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'po_id' => function () {
                return PurchaseOrder::inRandomOrder()->first()?->id ?? PurchaseOrder::factory();
            },
            'material_id' => function () {
                return Material::inRandomOrder()->first()?->id ?? Material::factory();
            },
            'name' => $this->faker->word,
            'qty' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'subtotal' => function (array $attributes) {
                return $attributes['qty'] * $attributes['price'];
            },
        ];
    }
}