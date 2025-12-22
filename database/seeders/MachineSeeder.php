<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = [];

        // Create admin users for each machine type
        $adminPotong = User::firstOrCreate([
            'email' => 'miskidi@example.com'
        ], [
            'name' => 'Miskidi',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminPlong = User::firstOrCreate([
            'email' => 'fanti@example.com'
        ], [
            'name' => 'Fanti',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminPress = User::firstOrCreate([
            'email' => 'salim@example.com'
        ], [
            'name' => 'Salim',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminLaspen = User::firstOrCreate([
            'email' => 'rudi@example.com'
        ], [
            'name' => 'Rudi',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminLasMig = User::firstOrCreate([
            'email' => 'rudi2@example.com'
        ], [
            'name' => 'Rudi',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminPhosphating = User::firstOrCreate([
            'email' => 'dimas@example.com'
        ], [
            'name' => 'Dimas',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminCat = User::firstOrCreate([
            'email' => 'anam@example.com'
        ], [
            'name' => 'Anam',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        $adminPacking = User::firstOrCreate([
            'email' => 'tega@example.com'
        ], [
            'name' => 'Tega',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'permissions' => json_encode(['view_machines', 'manage_machines'])
        ]);

        // Mesin Potong: 8 mesin
        for ($i = 1; $i <= 8; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Potong ' . $i,
                'email' => 'operator.potong' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Potong ' . $i,
                'email' => 'pic.potong' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-POTONG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Potong ' . $i,
                'type' => 'POTONG',
                'capacity_per_hour' => 200,
                'status' => $i % 2 == 0 ? 'RUNNING' : 'IDLE',
                'personnel' => [
                    [
                        'id' => $adminPotong->id,
                        'name' => $adminPotong->name,
                        'position' => 'Admin Mesin Potong',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => false,
            ];
        }

        // Mesin Plong: 50 mesin
        for ($i = 1; $i <= 50; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Plong ' . $i,
                'email' => 'operator.plong' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Plong ' . $i,
                'email' => 'pic.plong' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-PLONG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Plong ' . $i,
                'type' => 'PLONG',
                'capacity_per_hour' => 120,
                'status' => $i % 3 == 0 ? 'MAINTENANCE' : ($i % 2 == 0 ? 'RUNNING' : 'IDLE'),
                'personnel' => [
                    [
                        'id' => $adminPlong->id,
                        'name' => $adminPlong->name,
                        'position' => 'Admin Mesin Plong',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => $i % 3 == 0,
            ];
        }

        // Mesin Press: 11 mesin
        for ($i = 1; $i <= 11; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Press ' . $i,
                'email' => 'operator.press' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Press ' . $i,
                'email' => 'pic.press' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-PRESS-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Press ' . $i,
                'type' => 'PRESS',
                'capacity_per_hour' => 150,
                'status' => $i % 2 == 0 ? 'RUNNING' : 'IDLE',
                'personnel' => [
                    [
                        'id' => $adminPress->id,
                        'name' => $adminPress->name,
                        'position' => 'Admin Mesin Press',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => false,
            ];
        }

        // Mesin Laspen: 40 mesin
        for ($i = 1; $i <= 40; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Laspen ' . $i,
                'email' => 'operator.laspen' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Laspen ' . $i,
                'email' => 'pic.laspen' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-LASPEN-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Laspen ' . $i,
                'type' => 'LASPEN',
                'capacity_per_hour' => 100,
                'status' => $i % 4 == 0 ? 'MAINTENANCE' : ($i % 2 == 0 ? 'RUNNING' : 'IDLE'),
                'personnel' => [
                    [
                        'id' => $adminLaspen->id,
                        'name' => $adminLaspen->name,
                        'position' => 'Admin Mesin Laspen',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => $i % 4 == 0,
            ];
        }

        // Mesin Las Mig: 40 mesin
        for ($i = 1; $i <= 40; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Las Mig ' . $i,
                'email' => 'operator.lasmig' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Las Mig ' . $i,
                'email' => 'pic.lasmig' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-LASMIG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Las Mig ' . $i,
                'type' => 'LASMIG',
                'capacity_per_hour' => 90,
                'status' => $i % 4 == 0 ? 'MAINTENANCE' : ($i % 2 == 0 ? 'RUNNING' : 'IDLE'),
                'personnel' => [
                    [
                        'id' => $adminLasMig->id,
                        'name' => $adminLasMig->name,
                        'position' => 'Admin Mesin Las Mig',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => $i % 4 == 0,
            ];
        }

        // Phosphating: 2 mesin
        for ($i = 1; $i <= 2; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Phosphating ' . $i,
                'email' => 'operator.phosphating' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Phosphating ' . $i,
                'email' => 'pic.phosphating' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-PHOSPHATING-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Phosphating ' . $i,
                'type' => 'PHOSPHATING',
                'capacity_per_hour' => 80,
                'status' => $i % 2 == 0 ? 'RUNNING' : 'IDLE',
                'personnel' => [
                    [
                        'id' => $adminPhosphating->id,
                        'name' => $adminPhosphating->name,
                        'position' => 'Admin Phosphating',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => false,
            ];
        }

        // Cat: 4 mesin
        for ($i = 1; $i <= 4; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Cat ' . $i,
                'email' => 'operator.cat' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Cat ' . $i,
                'email' => 'pic.cat' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-CAT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Cat ' . $i,
                'type' => 'CAT',
                'capacity_per_hour' => 70,
                'status' => $i % 2 == 0 ? 'RUNNING' : 'IDLE',
                'personnel' => [
                    [
                        'id' => $adminCat->id,
                        'name' => $adminCat->name,
                        'position' => 'Admin Cat',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => false,
            ];
        }

        // Packing: 1 mesin
        for ($i = 1; $i <= 1; $i++) {
            // Create operator and pic for this machine
            $operator = User::create([
                'name' => 'Operator Packing ' . $i,
                'email' => 'operator.packing' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'operate_machines'])
            ]);

            $pic = User::create([
                'name' => 'PIC Packing ' . $i,
                'email' => 'pic.packing' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'permissions' => json_encode(['view_machines', 'quality_control'])
            ]);

            $machines[] = [
                'code' => 'MCH-PACKING-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Packing ' . $i,
                'type' => 'PACKING',
                'capacity_per_hour' => 100,
                'status' => 'RUNNING',
                'personnel' => [
                    [
                        'id' => $adminPacking->id,
                        'name' => $adminPacking->name,
                        'position' => 'Admin Packing',
                    ],
                    [
                        'id' => $operator->id,
                        'name' => $operator->name,
                        'position' => 'Operator',
                    ],
                    [
                        'id' => $pic->id,
                        'name' => $pic->name,
                        'position' => 'PIC',
                    ],
                ],
                'is_maintenance' => false,
            ];
        }

        foreach ($machines as $machine) {
            Machine::create($machine);
        }
    }
}