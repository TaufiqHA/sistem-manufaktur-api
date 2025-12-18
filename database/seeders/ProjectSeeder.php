<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the table exists and is empty before seeding
        if (!Schema::hasTable('projects')) {
            $this->command->error('Projects table does not exist. Skipping ProjectSeeder.');
            return;
        }

        $projectsExist = Project::count() > 0;
        
        if ($projectsExist) {
            $this->command->info('Projects already exist. Skipping ProjectSeeder.');
            return;
        }

        $this->command->info('Seeding projects...');

        // Sample manufacturing projects
        $projects = [
            [
                'code' => 'PROJ-001',
                'name' => 'Automotive Parts Manufacturing',
                'customer' => 'Automotive Solutions Inc.',
                'start_date' => now()->subDays(30),
                'deadline' => now()->addDays(120),
                'status' => 'IN_PROGRESS',
                'progress' => 45,
                'qty_per_unit' => 500,
                'procurement_qty' => 250000,
                'total_qty' => 250000,
                'unit' => 'PCS',
                'is_locked' => false,
            ],
            [
                'code' => 'PROJ-002',
                'name' => 'Electronics Assembly Line',
                'customer' => 'Tech Innovations Ltd.',
                'start_date' => now()->subDays(15),
                'deadline' => now()->addDays(180),
                'status' => 'IN_PROGRESS',
                'progress' => 30,
                'qty_per_unit' => 1000,
                'procurement_qty' => 500000,
                'total_qty' => 500000,
                'unit' => 'SET',
                'is_locked' => false,
            ],
            [
                'code' => 'PROJ-003',
                'name' => 'Medical Device Production',
                'customer' => 'Healthcare Equipment Co.',
                'start_date' => now()->subDays(45),
                'deadline' => now()->addDays(90),
                'status' => 'IN_PROGRESS',
                'progress' => 65,
                'qty_per_unit' => 250,
                'procurement_qty' => 125000,
                'total_qty' => 125000,
                'unit' => 'UNIT',
                'is_locked' => true,
            ],
            [
                'code' => 'PROJ-004',
                'name' => 'Aerospace Components',
                'customer' => 'AeroTech Industries',
                'start_date' => now()->addDays(10),
                'deadline' => now()->addDays(200),
                'status' => 'PLANNED',
                'progress' => 0,
                'qty_per_unit' => 150,
                'procurement_qty' => 22500,
                'total_qty' => 22500,
                'unit' => 'KIT',
                'is_locked' => false,
            ],
            [
                'code' => 'PROJ-005',
                'name' => 'Industrial Machinery',
                'customer' => 'Machinery Builders Inc.',
                'start_date' => now()->subDays(60),
                'deadline' => now()->subDays(10),
                'status' => 'COMPLETED',
                'progress' => 100,
                'qty_per_unit' => 75,
                'procurement_qty' => 5625,
                'total_qty' => 5625,
                'unit' => 'UNIT',
                'is_locked' => true,
            ],
            [
                'code' => 'PROJ-006',
                'name' => 'Textile Manufacturing Setup',
                'customer' => 'Fashion Manufacturing Co.',
                'start_date' => now()->subDays(5),
                'deadline' => now()->addDays(150),
                'status' => 'IN_PROGRESS',
                'progress' => 15,
                'qty_per_unit' => 2000,
                'procurement_qty' => 4000000,
                'total_qty' => 4000000,
                'unit' => 'METER',
                'is_locked' => false,
            ],
            [
                'code' => 'PROJ-007',
                'name' => 'Food Processing Equipment',
                'customer' => 'FoodTech Solutions',
                'start_date' => now()->addDays(30),
                'deadline' => now()->addDays(210),
                'status' => 'PLANNED',
                'progress' => 0,
                'qty_per_unit' => 85,
                'procurement_qty' => 7225,
                'total_qty' => 7225,
                'unit' => 'SET',
                'is_locked' => false,
            ],
            [
                'code' => 'PROJ-008',
                'name' => 'Pharmaceutical Production',
                'customer' => 'Pharma Manufacturing Ltd.',
                'start_date' => now()->subDays(90),
                'deadline' => now()->subDays(30),
                'status' => 'ON_HOLD',
                'progress' => 75,
                'qty_per_unit' => 300,
                'procurement_qty' => 225000,
                'total_qty' => 225000,
                'unit' => 'UNIT',
                'is_locked' => true,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('Projects seeded successfully.');
    }
}