<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FinishedGoodsWarehouse;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class FinishedGoodsWarehouseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    public function it_can_list_finished_goods_warehouses(): void
    {
        $user = User::factory()->create();

        $finishedGoodsWarehouse = FinishedGoodsWarehouse::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/finished-goods-warehouses');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $finishedGoodsWarehouse->id,
                'project_id' => $finishedGoodsWarehouse->project_id,
                'item_name' => $finishedGoodsWarehouse->item_name,
                'total_produced' => $finishedGoodsWarehouse->total_produced,
                'shipped_qty' => $finishedGoodsWarehouse->shipped_qty,
                'available_stock' => $finishedGoodsWarehouse->available_stock,
                'unit' => $finishedGoodsWarehouse->unit,
            ]);
    }

    /** @test */
    public function it_can_create_a_finished_goods_warehouse(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create();

        $data = [
            'project_id' => $project->id,
            'item_name' => 'Test Finished Product',
            'total_produced' => 100,
            'shipped_qty' => 20,
            'available_stock' => 80,
            'unit' => 'pcs',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/finished-goods-warehouses', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('finished_goods_warehouses', [
            'project_id' => $data['project_id'],
            'item_name' => $data['item_name'],
            'total_produced' => $data['total_produced'],
            'shipped_qty' => $data['shipped_qty'],
            'available_stock' => $data['available_stock'],
            'unit' => $data['unit'],
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_finished_goods_warehouse(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/finished-goods-warehouses', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'project_id',
                    'item_name',
                    'total_produced',
                    'shipped_qty',
                    'available_stock',
                    'unit',
                ]
            ]);
    }

    /** @test */
    public function it_can_show_a_finished_goods_warehouse(): void
    {
        $user = User::factory()->create();

        $finishedGoodsWarehouse = FinishedGoodsWarehouse::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/finished-goods-warehouses/{$finishedGoodsWarehouse->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $finishedGoodsWarehouse->id,
                'project_id' => $finishedGoodsWarehouse->project_id,
                'item_name' => $finishedGoodsWarehouse->item_name,
                'total_produced' => $finishedGoodsWarehouse->total_produced,
                'shipped_qty' => $finishedGoodsWarehouse->shipped_qty,
                'available_stock' => $finishedGoodsWarehouse->available_stock,
                'unit' => $finishedGoodsWarehouse->unit,
            ]);
    }

    /** @test */
    public function it_can_update_a_finished_goods_warehouse(): void
    {
        $user = User::factory()->create();

        $finishedGoodsWarehouse = FinishedGoodsWarehouse::factory()->create();

        $updatedData = [
            'project_id' => $finishedGoodsWarehouse->project_id,
            'item_name' => 'Updated Finished Product',
            'total_produced' => 150,
            'shipped_qty' => 30,
            'available_stock' => 120,
            'unit' => 'units',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/finished-goods-warehouses/{$finishedGoodsWarehouse->id}", $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('finished_goods_warehouses', [
            'id' => $finishedGoodsWarehouse->id,
            'item_name' => $updatedData['item_name'],
            'total_produced' => $updatedData['total_produced'],
            'shipped_qty' => $updatedData['shipped_qty'],
            'available_stock' => $updatedData['available_stock'],
            'unit' => $updatedData['unit'],
        ]);
    }

    /** @test */
    public function it_can_delete_a_finished_goods_warehouse(): void
    {
        $user = User::factory()->create();

        $finishedGoodsWarehouse = FinishedGoodsWarehouse::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/finished-goods-warehouses/{$finishedGoodsWarehouse->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('finished_goods_warehouses', [
            'id' => $finishedGoodsWarehouse->id,
        ]);
    }

    /** @test */
    public function it_prevents_creating_finished_goods_warehouse_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $invalidData = [
            'project_id' => 999999, // Non-existent project
            'item_name' => '', // Empty item name
            'total_produced' => -1, // Negative value
            'shipped_qty' => -1, // Negative value
            'available_stock' => -1, // Negative value
            'unit' => '', // Empty unit
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/finished-goods-warehouses', $invalidData);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_prevents_updating_finished_goods_warehouse_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $finishedGoodsWarehouse = FinishedGoodsWarehouse::factory()->create();

        $invalidData = [
            'project_id' => 999999, // Non-existent project
            'item_name' => '', // Empty item name
            'total_produced' => -1, // Negative value
            'shipped_qty' => -1, // Negative value
            'available_stock' => -1, // Negative value
            'unit' => '', // Empty unit
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/finished-goods-warehouses/{$finishedGoodsWarehouse->id}", $invalidData);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_prevents_creating_finished_goods_warehouse_with_available_stock_exceeding_total(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create();

        $invalidData = [
            'project_id' => $project->id,
            'item_name' => 'Test Product',
            'total_produced' => 100,
            'shipped_qty' => 20,
            'available_stock' => 150, // This exceeds total_produced (100)
            'unit' => 'pcs',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/finished-goods-warehouses', $invalidData);

        $response->assertStatus(422);
    }
}