<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user with all permissions
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'permissions' => json_encode([
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'view_products',
                'create_products',
                'edit_products',
                'delete_products',
                'view_orders',
                'create_orders',
                'edit_orders',
                'delete_orders',
                'view_reports',
                'manage_inventory',
                'manage_finances',
                'access_dashboard'
            ])
        ]);

        // // Create operator user with limited permissions
        // User::factory()->create([
        //     'name' => 'Operator User',
        //     'email' => 'operator@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'operator',
        //     'permissions' => json_encode([
        //         'view_products',
        //         'view_orders',
        //         'create_orders',
        //         'edit_orders'
        //     ])
        // ]);

        // // Create manager user with management permissions
        // User::factory()->create([
        //     'name' => 'Manager User',
        //     'email' => 'manager@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'manager',
        //     'permissions' => json_encode([
        //         'view_users',
        //         'view_products',
        //         'create_products',
        //         'edit_products',
        //         'view_orders',
        //         'create_orders',
        //         'edit_orders',
        //         'view_reports',
        //         'manage_inventory'
        //     ])
        // ]);

        // // Create regular users with minimal permissions
        // User::factory(5)->create([
        //     'password' => Hash::make('password'),
        //     'permissions' => json_encode([
        //         'view_products',
        //         'view_orders'
        //     ])
        // ]);
    }
}
