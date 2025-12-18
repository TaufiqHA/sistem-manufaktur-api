<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Machine;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::factory();
        $projectItem = ProjectItem::factory();
        $machine = Machine::factory();

        return [
            'project_id' => $project->create()->id, // Create and use the ID (will be cast to integer in model)
            'project_name' => fake()->sentence(3),
            'item_id' => $projectItem->create()->id, // Create and use the ID (will be cast to integer in model)
            'item_name' => fake()->word,
            'step' => fake()->sentence(2),
            'machine_id' => $machine->create()->id, // Create and use the ID (will be cast to integer in model)
            'target_qty' => fake()->numberBetween(1, 1000),
            'completed_qty' => 0,
            'defect_qty' => 0,
            'status' => fake()->randomElement(['PENDING', 'IN_PROGRESS', 'PAUSED', 'COMPLETED', 'DOWNTIME']),
            'downtime_start' => null,
            'total_downtime_minutes' => 0,
        ];
    }

    /**
     * Indicate that the task has some completed quantity.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'IN_PROGRESS',
            'completed_qty' => fake()->numberBetween(1, $attributes['target_qty'] - 1),
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'COMPLETED',
            'completed_qty' => $attributes['target_qty'],
        ]);
    }

    /**
     * Indicate that the task is paused.
     */
    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'PAUSED',
            'completed_qty' => fake()->numberBetween(0, $attributes['target_qty'] - 1),
        ]);
    }

    /**
     * Indicate that the task is in downtime.
     */
    public function downtime(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'DOWNTIME',
            'downtime_start' => now(),
            'total_downtime_minutes' => fake()->numberBetween(0, 480), // Up to 8 hours in minutes
        ]);
    }
}