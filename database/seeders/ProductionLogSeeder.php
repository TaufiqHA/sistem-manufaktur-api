<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Machine;
use App\Models\ProjectItem;
use App\Models\Project;
use App\Models\ProductionLog;

class ProductionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if related records exist, if not create them
        if (Project::count() === 0) {
            $this->command->info('Creating sample projects since none exist...');
            Project::factory()->count(5)->create();
        }
        
        if (Machine::count() === 0) {
            $this->command->info('Creating sample machines since none exist...');
            Machine::factory()->count(3)->create();
        }
        
        if (ProjectItem::count() === 0) {
            $this->command->info('Creating sample project items since none exist...');
            ProjectItem::factory()->count(10)->create();
        }
        
        if (Task::count() === 0) {
            $this->command->info('Creating sample tasks since none exist...');
            $projects = Project::all();
            $machines = Machine::all();
            $projectItems = ProjectItem::all();
            
            foreach ($projects as $project) {
                foreach ($machines as $machine) {
                    foreach ($projectItems->random(2) as $item) {
                        Task::factory()->create([
                            'project_id' => $project->id,
                            'project_name' => $project->name,
                            'item_id' => $item->id,
                            'item_name' => $item->name,
                            'machine_id' => $machine->id,
                        ]);
                    }
                }
            }
        }

        // Clear existing production logs if needed
        DB::table('production_logs')->truncate();

        // Get all related records
        $tasks = Task::all();
        $machines = Machine::all();
        $projectItems = ProjectItem::all();
        $projects = Project::all();

        // Create sample production logs
        $this->command->info('Seeding production logs...');
        
        for ($i = 0; $i < 50; $i++) {
            $task = $tasks->random();
            $machine = $machines->random();
            $item = $projectItems->random();
            $project = $projects->random();
            
            // Determine shift
            $shifts = ['SHIFT_1', 'SHIFT_2', 'SHIFT_3'];
            $shift = $shifts[array_rand($shifts)];
            
            // Determine type
            $types = ['OUTPUT', 'DOWNTIME_START', 'DOWNTIME_END'];
            $type = $types[array_rand($types)];
            
            // For OUTPUT logs, ensure good_qty is significant
            if ($type === 'OUTPUT') {
                $goodQty = rand(50, 200);
                $defectQty = rand(0, 20);
            } else {
                // For downtime events, quantities may be 0
                $goodQty = rand(0, 5);
                $defectQty = rand(0, 5);
            }
            
            ProductionLog::create([
                'task_id' => $task->id,
                'machine_id' => $machine->id,
                'item_id' => $item->id,
                'project_id' => $project->id,
                'step' => fake()->word(),
                'shift' => $shift,
                'good_qty' => $goodQty,
                'defect_qty' => $defectQty,
                'operator' => fake()->name(),
                'logged_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'type' => $type,
            ]);
        }
        
        $this->command->info('Production logs seeded successfully!');
    }
}