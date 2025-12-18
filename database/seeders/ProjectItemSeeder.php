<?php

namespace Database\Seeders;

use App\Models\ProjectItem;
use Illuminate\Database\Seeder;

class ProjectItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample project items
        $projectItems = [
            [
                'project_id' => '1',
                'name' => 'Table Top',
                'dimensions' => '1200x600x18',
                'thickness' => '18mm',
                'qty_set' => 1,
                'quantity' => 5,
                'unit' => 'pcs',
                'is_bom_locked' => false,
                'is_workflow_locked' => false,
                'workflow' => json_encode([
                    ['step' => 'cutting', 'status' => 'pending'],
                    ['step' => 'sanding', 'status' => 'pending'],
                    ['step' => 'assembly', 'status' => 'pending'],
                ]),
            ],
            [
                'project_id' => '1',
                'name' => 'Table Leg',
                'dimensions' => '700x70x70',
                'thickness' => '70mm',
                'qty_set' => 4,
                'quantity' => 20,
                'unit' => 'pcs',
                'is_bom_locked' => false,
                'is_workflow_locked' => false,
                'workflow' => json_encode([
                    ['step' => 'cutting', 'status' => 'pending'],
                    ['step' => 'turning', 'status' => 'pending'],
                    ['step' => 'sanding', 'status' => 'pending'],
                ]),
            ],
            [
                'project_id' => '2',
                'name' => 'Chair Seat',
                'dimensions' => '450x450x15',
                'thickness' => '15mm',
                'qty_set' => 1,
                'quantity' => 8,
                'unit' => 'pcs',
                'is_bom_locked' => true,
                'is_workflow_locked' => false,
                'workflow' => json_encode([
                    ['step' => 'cutting', 'status' => 'completed'],
                    ['step' => 'sanding', 'status' => 'pending'],
                    ['step' => 'upholstery', 'status' => 'pending'],
                ]),
            ],
            [
                'project_id' => '2',
                'name' => 'Chair Backrest',
                'dimensions' => '480x300x12',
                'thickness' => '12mm',
                'qty_set' => 1,
                'quantity' => 8,
                'unit' => 'pcs',
                'is_bom_locked' => true,
                'is_workflow_locked' => false,
                'workflow' => json_encode([
                    ['step' => 'cutting', 'status' => 'completed'],
                    ['step' => 'sanding', 'status' => 'completed'],
                    ['step' => 'assembly', 'status' => 'pending'],
                ]),
            ],
            [
                'project_id' => '3',
                'name' => 'Shelf Panel',
                'dimensions' => '800x250x18',
                'thickness' => '18mm',
                'qty_set' => 5,
                'quantity' => 25,
                'unit' => 'pcs',
                'is_bom_locked' => false,
                'is_workflow_locked' => true,
                'workflow' => json_encode([
                    ['step' => 'cutting', 'status' => 'pending'],
                    ['step' => 'sanding', 'status' => 'pending'],
                    ['step' => 'edge_banding', 'status' => 'pending'],
                ]),
            ],
        ];

        foreach ($projectItems as $item) {
            ProjectItem::create($item);
        }

        // Create additional project items using factory
        ProjectItem::factory()->count(10)->create();
    }
}