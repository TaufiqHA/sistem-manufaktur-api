<?php

namespace Database\Factories;

use App\Models\Backup;
use Illuminate\Database\Eloquent\Factories\Factory;

class BackupFactory extends Factory
{
    protected $model = Backup::class;

    public function definition(): array
    {
        return [
            'filename' => 'backup_' . $this->faker->unixTime . '.sql',
            'path' => 'backups/' . now()->format('Y/m/') . '/',
            'disk' => $this->faker->randomElement(['local', 's3', 'ftp']),
            'size' => $this->faker->randomElement([null, $this->faker->numberBetween(100000, 10000000)]),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
            'type' => $this->faker->randomElement(['full', 'incremental', 'selective']),
            'details' => [
                'tables' => $this->faker->randomElements(['users', 'products', 'orders', 'categories'], 2),
                'excluded_tables' => [],
                'compression' => $this->faker->boolean ? 'gzip' : null,
                'encrypted' => $this->faker->boolean
            ],
            'completed_at' => $this->faker->randomElement([null, now()->subDays(rand(0, 30))]),
            'created_by' => $this->faker->userName
        ];
    }
}