<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectItem>
 */
class ProjectItemFactory extends Factory
{
    protected $model = ProjectItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->sentence(3),
            'dimensions' => fake()->word() . 'x' . fake()->word() . 'x' . fake()->word(),
            'thickness' => fake()->randomElement(['3mm', '6mm', '9mm', '12mm', '15mm', '18mm', '21mm', '24mm']),
            'qty_set' => fake()->numberBetween(1, 100),
            'quantity' => fake()->numberBetween(1, 1000),
            'unit' => fake()->randomElement(['pcs', 'set', 'unit', 'box', 'pack', 'sheet']),
            'is_bom_locked' => fake()->boolean,
            'is_workflow_locked' => fake()->boolean,
            'workflow' => [],
        ];
    }
}