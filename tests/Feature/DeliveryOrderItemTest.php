<?php

namespace Tests\Feature;

use App\Models\DeliveryOrder;
use App\Models\FinishedGoodsWarehouse;
use App\Models\Project;
use App\Models\DeliveryOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryOrderItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = \App\Models\User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    /** @test */
    public function it_can_list_delivery_order_items()
    {
        // Create related models first
        $deliveryOrder = DeliveryOrder::factory()->create();
        $warehouse = FinishedGoodsWarehouse::factory()->create();
        $project = Project::factory()->create();

        // Then create a delivery order item with specific relationships
        $deliveryOrderItem = DeliveryOrderItem::factory()->create([
            'delivery_order_id' => $deliveryOrder->id,
            'warehouse_id' => $warehouse->id,
            'project_id' => $project->id,
        ]);

        $response = $this->getJson('/api/delivery-order-items');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJson([
                     'data' => [
                         [
                             'id' => $deliveryOrderItem->id,
                             'project_name' => $deliveryOrderItem->project_name,
                             'item_name' => $deliveryOrderItem->item_name,
                             'qty' => $deliveryOrderItem->qty,
                             'unit' => $deliveryOrderItem->unit,
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function it_can_show_a_delivery_order_item()
    {
        // Create related models first
        $deliveryOrder = DeliveryOrder::factory()->create();
        $warehouse = FinishedGoodsWarehouse::factory()->create();
        $project = Project::factory()->create();

        // Create a delivery order item with specific relationships
        $deliveryOrderItem = DeliveryOrderItem::factory()->create([
            'delivery_order_id' => $deliveryOrder->id,
            'warehouse_id' => $warehouse->id,
            'project_id' => $project->id,
        ]);

        $response = $this->getJson("/api/delivery-order-items/{$deliveryOrderItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $deliveryOrderItem->id,
                     'project_name' => $deliveryOrderItem->project_name,
                     'item_name' => $deliveryOrderItem->item_name,
                     'qty' => $deliveryOrderItem->qty,
                     'unit' => $deliveryOrderItem->unit,
                 ]);
    }

    /** @test */
    public function it_can_create_a_delivery_order_item()
    {
        // Create related models
        $deliveryOrder = DeliveryOrder::factory()->create();
        $warehouse = FinishedGoodsWarehouse::factory()->create();
        $project = Project::factory()->create();

        $data = [
            'delivery_order_id' => $deliveryOrder->id,
            'warehouse_id' => $warehouse->id,
            'project_id' => $project->id,
            'project_name' => 'Test Project',
            'item_name' => 'Test Item',
            'qty' => 10,
            'unit' => 'pcs',
        ];

        $response = $this->postJson('/api/delivery-order-items', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('delivery_order_items', [
            'project_name' => 'Test Project',
            'item_name' => 'Test Item',
            'qty' => 10,
            'unit' => 'pcs',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_delivery_order_item()
    {
        $data = [
            'delivery_order_id' => null,
            'warehouse_id' => null,
            'project_id' => null,
            'project_name' => '',
            'item_name' => '',
            'qty' => null,
            'unit' => '',
        ];

        $response = $this->postJson('/api/delivery-order-items', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         'delivery_order_id',
                         'warehouse_id',
                         'project_id',
                         'project_name',
                         'item_name',
                         'qty',
                         'unit',
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_a_delivery_order_item()
    {
        // Create related models first
        $originalDeliveryOrder = DeliveryOrder::factory()->create();
        $originalWarehouse = FinishedGoodsWarehouse::factory()->create();
        $originalProject = Project::factory()->create();

        // Create a delivery order item with specific relationships
        $deliveryOrderItem = DeliveryOrderItem::factory()->create([
            'delivery_order_id' => $originalDeliveryOrder->id,
            'warehouse_id' => $originalWarehouse->id,
            'project_id' => $originalProject->id,
        ]);

        // Create new related models for the update
        $deliveryOrder = DeliveryOrder::factory()->create();
        $warehouse = FinishedGoodsWarehouse::factory()->create();
        $project = Project::factory()->create();

        $data = [
            'delivery_order_id' => $deliveryOrder->id,
            'warehouse_id' => $warehouse->id,
            'project_id' => $project->id,
            'project_name' => 'Updated Project',
            'item_name' => 'Updated Item',
            'qty' => 20,
            'unit' => 'boxes',
        ];

        $response = $this->putJson("/api/delivery-order-items/{$deliveryOrderItem->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('delivery_order_items', [
            'id' => $deliveryOrderItem->id,
            'project_name' => 'Updated Project',
            'item_name' => 'Updated Item',
            'qty' => 20,
            'unit' => 'boxes',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_updating_a_delivery_order_item()
    {
        $deliveryOrderItem = DeliveryOrderItem::factory()->create();

        $data = [
            'delivery_order_id' => null,
            'warehouse_id' => null,
            'project_id' => null,
            'project_name' => '',
            'item_name' => '',
            'qty' => null,
            'unit' => '',
        ];

        $response = $this->putJson("/api/delivery-order-items/{$deliveryOrderItem->id}", $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         'delivery_order_id',
                         'warehouse_id',
                         'project_id',
                         'project_name',
                         'item_name',
                         'qty',
                         'unit',
                     ]
                 ]);
    }

    /** @test */
    public function it_can_delete_a_delivery_order_item()
    {
        // Create a delivery order item
        $deliveryOrderItem = DeliveryOrderItem::factory()->create();

        $response = $this->deleteJson("/api/delivery-order-items/{$deliveryOrderItem->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('delivery_order_items', [
            'id' => $deliveryOrderItem->id,
        ]);
    }

    /** @test */
    public function it_returns_404_when_trying_to_show_a_non_existing_delivery_order_item()
    {
        $response = $this->getJson('/api/delivery-order-items/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_trying_to_update_a_non_existing_delivery_order_item()
    {
        $data = [
            'delivery_order_id' => 1,
            'warehouse_id' => 1,
            'project_id' => 1,
            'project_name' => 'Updated Project',
            'item_name' => 'Updated Item',
            'qty' => 20,
            'unit' => 'boxes',
        ];

        $response = $this->putJson('/api/delivery-order-items/99999', $data);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_trying_to_delete_a_non_existing_delivery_order_item()
    {
        $response = $this->deleteJson('/api/delivery-order-items/99999');

        $response->assertStatus(404);
    }
}