<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = [
            [
                'code' => 'MCH-PRESS-001',
                'name' => 'Press Machine A',
                'type' => 'PRESS',
                'capacity_per_hour' => 150,
                'status' => 'RUNNING',
                'personnel' => [
                    [
                        'id' => '1',
                        'name' => 'Ahmad Kurnia',
                        'position' => 'Machine Operator',
                    ],
                    [
                        'id' => '2',
                        'name' => 'Budi Santoso',
                        'position' => 'Assistant Operator',
                    ],
                ],
                'is_maintenance' => false,
            ],
            [
                'code' => 'MCH-LAS-001',
                'name' => 'Laser Cutting Machine',
                'type' => 'LAS',
                'capacity_per_hour' => 80,
                'status' => 'IDLE',
                'personnel' => [
                    [
                        'id' => '3',
                        'name' => 'Rizki Pratama',
                        'position' => 'Laser Technician',
                    ],
                ],
                'is_maintenance' => false,
            ],
            [
                'code' => 'MCH-PLONG-001',
                'name' => 'Bending Machine',
                'type' => 'PLONG',
                'capacity_per_hour' => 120,
                'status' => 'MAINTENANCE',
                'personnel' => [
                    [
                        'id' => '4',
                        'name' => 'Fajar Nugraha',
                        'position' => 'Maintenance Engineer',
                    ],
                ],
                'is_maintenance' => true,
            ],
            [
                'code' => 'MCH-POTONG-001',
                'name' => 'Cutting Machine',
                'type' => 'POTONG',
                'capacity_per_hour' => 200,
                'status' => 'RUNNING',
                'personnel' => [
                    [
                        'id' => '5',
                        'name' => 'Indra Wijaya',
                        'position' => 'Machine Operator',
                    ],
                    [
                        'id' => '6',
                        'name' => 'Sigit Prasetya',
                        'position' => 'Quality Control',
                    ],
                ],
                'is_maintenance' => false,
            ],
            [
                'code' => 'MCH-WT-001',
                'name' => 'Welding Table',
                'type' => 'WT',
                'capacity_per_hour' => 60,
                'status' => 'OFFLINE',
                'personnel' => [
                    [
                        'id' => '7',
                        'name' => 'Agus Salim',
                        'position' => 'Welder',
                    ],
                ],
                'is_maintenance' => false,
            ],
            [
                'code' => 'MCH-POWDER-001',
                'name' => 'Powder Coating Unit',
                'type' => 'POWDER',
                'capacity_per_hour' => 40,
                'status' => 'IDLE',
                'personnel' => [
                    [
                        'id' => '8',
                        'name' => 'Hendra Kurniawan',
                        'position' => 'Coating Specialist',
                    ],
                ],
                'is_maintenance' => false,
            ],
            [
                'code' => 'MCH-QC-001',
                'name' => 'Quality Inspection Station',
                'type' => 'QC',
                'capacity_per_hour' => 100,
                'status' => 'DOWNTIME',
                'personnel' => [
                    [
                        'id' => '9',
                        'name' => 'Riski Ramadhan',
                        'position' => 'Quality Inspector',
                    ],
                ],
                'is_maintenance' => false,
            ],
        ];

        foreach ($machines as $machine) {
            Machine::create($machine);
        }
    }
}