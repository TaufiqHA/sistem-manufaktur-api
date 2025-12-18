<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            // Raw Materials
            [
                'code' => 'RAW-STEEL-001',
                'name' => 'Steel Plate',
                'unit' => 'kg',
                'current_stock' => 500,
                'safety_stock' => 100,
                'price_per_unit' => 15.50,
                'category' => 'RAW'
            ],
            [
                'code' => 'RAW-WOOD-001',
                'name' => 'Oak Wood Planks',
                'unit' => 'pcs',
                'current_stock' => 200,
                'safety_stock' => 50,
                'price_per_unit' => 25.00,
                'category' => 'RAW'
            ],
            [
                'code' => 'RAW-ALUM-001',
                'name' => 'Aluminum Rods',
                'unit' => 'meter',
                'current_stock' => 300,
                'safety_stock' => 75,
                'price_per_unit' => 8.75,
                'category' => 'RAW'
            ],
            [
                'code' => 'RAW-COPPER-001',
                'name' => 'Copper Wire',
                'unit' => 'meter',
                'current_stock' => 1000,
                'safety_stock' => 200,
                'price_per_unit' => 3.25,
                'category' => 'RAW'
            ],
            [
                'code' => 'RAW-PLASTIC-001',
                'name' => 'Plastic Sheets',
                'unit' => 'pcs',
                'current_stock' => 150,
                'safety_stock' => 30,
                'price_per_unit' => 12.99,
                'category' => 'RAW'
            ],

            // Finishing Materials
            [
                'code' => 'FIN-POLISH-001',
                'name' => 'Wood Polish',
                'unit' => 'liter',
                'current_stock' => 80,
                'safety_stock' => 20,
                'price_per_unit' => 18.50,
                'category' => 'FINISHING'
            ],
            [
                'code' => 'FIN-PAINT-001',
                'name' => 'Acrylic Paint Red',
                'unit' => 'liter',
                'current_stock' => 60,
                'safety_stock' => 15,
                'price_per_unit' => 22.00,
                'category' => 'FINISHING'
            ],
            [
                'code' => 'FIN-GLUE-001',
                'name' => 'Industrial Adhesive',
                'unit' => 'liter',
                'current_stock' => 45,
                'safety_stock' => 10,
                'price_per_unit' => 35.75,
                'category' => 'FINISHING'
            ],
            [
                'code' => 'FIN-VARNISH-001',
                'name' => 'Clear Varnish',
                'unit' => 'liter',
                'current_stock' => 55,
                'safety_stock' => 15,
                'price_per_unit' => 28.90,
                'category' => 'FINISHING'
            ],
            [
                'code' => 'FIN-SANDPAPER-001',
                'name' => 'Sandpaper Grit 120',
                'unit' => 'pcs',
                'current_stock' => 300,
                'safety_stock' => 50,
                'price_per_unit' => 2.50,
                'category' => 'FINISHING'
            ],

            // Hardware Components
            [
                'code' => 'HWD-BOLT-001',
                'name' => 'Steel Bolt M8x20mm',
                'unit' => 'pcs',
                'current_stock' => 1000,
                'safety_stock' => 200,
                'price_per_unit' => 0.45,
                'category' => 'HARDWARE'
            ],
            [
                'code' => 'HWD-NUT-001',
                'name' => 'Steel Nut M8',
                'unit' => 'pcs',
                'current_stock' => 1200,
                'safety_stock' => 250,
                'price_per_unit' => 0.25,
                'category' => 'HARDWARE'
            ],
            [
                'code' => 'HWD-WASHER-001',
                'name' => 'Flat Washer M8',
                'unit' => 'pcs',
                'current_stock' => 800,
                'safety_stock' => 150,
                'price_per_unit' => 0.15,
                'category' => 'HARDWARE'
            ],
            [
                'code' => 'HWD-SCREW-001',
                'name' => 'Wood Screw 4x30mm',
                'unit' => 'pcs',
                'current_stock' => 1500,
                'safety_stock' => 300,
                'price_per_unit' => 0.35,
                'category' => 'HARDWARE'
            ],
            [
                'code' => 'HWD-HINGE-001',
                'name' => 'Steel Hinge 4 inch',
                'unit' => 'pcs',
                'current_stock' => 100,
                'safety_stock' => 20,
                'price_per_unit' => 8.95,
                'category' => 'HARDWARE'
            ],

            // Low Stock Items (for testing low stock alerts)
            [
                'code' => 'LOW-STEEL-001',
                'name' => 'Special Steel Alloy',
                'unit' => 'kg',
                'current_stock' => 5,
                'safety_stock' => 50,
                'price_per_unit' => 45.00,
                'category' => 'RAW'
            ],
            [
                'code' => 'LOW-POLISH-001',
                'name' => 'Premium Wood Polish',
                'unit' => 'liter',
                'current_stock' => 8,
                'safety_stock' => 25,
                'price_per_unit' => 32.50,
                'category' => 'FINISHING'
            ],
        ];

        foreach ($materials as $materialData) {
            Material::create($materialData);
        }
    }
}
