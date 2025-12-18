<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample tasks using the factory
        Task::factory()->count(20)->create();
        
        // Create some specific example tasks
        Task::factory()->create([
            'project_id' => 1,
            'project_name' => 'Sample Project A',
            'item_id' => 1,
            'item_name' => 'Main Component',
            'step' => 'Cutting',
            'machine_id' => 1,
            'target_qty' => 100,
            'completed_qty' => 75,
            'defect_qty' => 2,
            'status' => 'IN_PROGRESS',
        ]);
        
        Task::factory()->create([
            'project_id' => 1,
            'project_name' => 'Sample Project A',
            'item_id' => 2,
            'item_name' => 'Secondary Component',
            'step' => 'Drilling',
            'machine_id' => 2,
            'target_qty' => 50,
            'completed_qty' => 50,
            'defect_qty' => 0,
            'status' => 'COMPLETED',
        ]);
        
        Task::factory()->create([
            'project_id' => 2,
            'project_name' => 'Sample Project B',
            'item_id' => 3,
            'item_name' => 'Assembly Unit',
            'step' => 'Assembly',
            'machine_id' => 3,
            'target_qty' => 25,
            'completed_qty' => 0,
            'defect_qty' => 0,
            'status' => 'PENDING',
        ]);
        
        Task::factory()->create([
            'project_id' => 2,
            'project_name' => 'Sample Project B',
            'item_id' => 4,
            'item_name' => 'Quality Check',
            'step' => 'Quality Control',
            'machine_id' => 4,
            'target_qty' => 25,
            'completed_qty' => 10,
            'defect_qty' => 1,
            'status' => 'DOWNTIME',
            'downtime_start' => now()->subHours(2),
            'total_downtime_minutes' => 120,
        ]);
    }
}