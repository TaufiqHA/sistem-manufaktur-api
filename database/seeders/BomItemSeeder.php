<?php

namespace Database\Seeders;

use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BomItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure related records exist before creating BOM items
        if (ProjectItem::count() === 0) {
            $this->command->info('Creating required ProjectItems for BomItemSeeder...');
            ProjectItem::factory()->count(10)->create();
        }

        if (Material::count() === 0) {
            $this->command->info('Creating required Materials for BomItemSeeder...');
            Material::factory()->count(15)->create();
        }

        // Clear existing records if re-running the seeder
        BomItem::truncate();

        // Create sample BOM items using the factory
        BomItem::factory()->count(20)->create();

        $this->command->info('BOM Items table seeded with 20 records.');
    }
}