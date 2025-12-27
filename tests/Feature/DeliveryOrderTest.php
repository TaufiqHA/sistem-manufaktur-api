<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DeliveryOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class DeliveryOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_delivery_orders(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create some delivery orders
        $deliveryOrders = DeliveryOrder::factory()->count(3)->create();

        // Make the request
        $response = $this->getJson('/api/delivery-orders');

        // Assert the response
        $response->assertStatus(200)
                 ->assertJsonCount(3)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'code',
                         'date',
                         'customer',
                         'address',
                         'driver_name',
                         'vehicle_plate',
                         'status',
                         'note',
                         'created_at',
                         'updated_at',
                     ]
                 ]);
    }

    public function test_can_get_single_delivery_order(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a delivery order
        $deliveryOrder = DeliveryOrder::factory()->create();

        // Make the request
        $response = $this->getJson("/api/delivery-orders/{$deliveryOrder->id}");

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $deliveryOrder->id,
                     'code' => $deliveryOrder->code,
                     'date' => $deliveryOrder->date->toISOString(),
                     'customer' => $deliveryOrder->customer,
                     'address' => $deliveryOrder->address,
                     'driver_name' => $deliveryOrder->driver_name,
                     'vehicle_plate' => $deliveryOrder->vehicle_plate,
                     'status' => $deliveryOrder->status,
                     'note' => $deliveryOrder->note,
                 ]);
    }

    public function test_can_create_delivery_order(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Prepare the data
        $data = [
            'code' => 'DO-TEST-12345',
            'date' => '2025-12-31 10:00:00',
            'customer' => 'Test Customer',
            'address' => '123 Test Street, Test City',
            'driver_name' => 'John Doe',
            'vehicle_plate' => 'ABC-1234',
            'status' => 'draft',
            'note' => 'This is a test note',
        ];

        // Make the request
        $response = $this->postJson('/api/delivery-orders', $data);

        // Assert the response
        $response->assertStatus(201)
                 ->assertJson([
                     'code' => $data['code'],
                     'customer' => $data['customer'],
                     'address' => $data['address'],
                     'driver_name' => $data['driver_name'],
                     'vehicle_plate' => $data['vehicle_plate'],
                     'status' => $data['status'],
                     'note' => $data['note'],
                 ]);

        // Assert the record was created in the database
        $this->assertDatabaseHas('delivery_orders', [
            'code' => $data['code'],
            'customer' => $data['customer'],
            'address' => $data['address'],
            'driver_name' => $data['driver_name'],
            'vehicle_plate' => $data['vehicle_plate'],
            'status' => $data['status'],
            'note' => $data['note'],
        ]);
    }

    public function test_cannot_create_delivery_order_without_required_fields(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Make the request with missing required fields
        $response = $this->postJson('/api/delivery-orders', []);

        // Assert the response
        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    public function test_cannot_create_delivery_order_with_duplicate_code(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a delivery order first
        $existingDeliveryOrder = DeliveryOrder::factory()->create();

        // Prepare data with duplicate code
        $data = [
            'code' => $existingDeliveryOrder->code,
            'date' => '2025-12-31 10:00:00',
            'customer' => 'Test Customer',
            'address' => '123 Test Street, Test City',
            'driver_name' => 'John Doe',
            'vehicle_plate' => 'ABC-1234',
        ];

        // Make the request
        $response = $this->postJson('/api/delivery-orders', $data);

        // Assert the response
        $response->assertStatus(422)
                 ->assertJson([
                     'errors' => [
                         'code' => ['The code has already been taken.'],
                     ]
                 ]);
    }

    public function test_can_update_delivery_order(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a delivery order
        $deliveryOrder = DeliveryOrder::factory()->create();

        // Prepare update data
        $data = [
            'code' => 'DO-UPDATED-54321',
            'date' => '2025-12-31 15:00:00',
            'customer' => 'Updated Customer',
            'address' => '456 Updated Street, Updated City',
            'driver_name' => 'Jane Doe',
            'vehicle_plate' => 'XYZ-5678',
            'status' => 'validated',
            'note' => 'Updated note',
        ];

        // Make the request
        $response = $this->putJson("/api/delivery-orders/{$deliveryOrder->id}", $data);

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $deliveryOrder->id,
                     'code' => $data['code'],
                     'customer' => $data['customer'],
                     'address' => $data['address'],
                     'driver_name' => $data['driver_name'],
                     'vehicle_plate' => $data['vehicle_plate'],
                     'status' => $data['status'],
                     'note' => $data['note'],
                 ]);

        // Assert the record was updated in the database
        $this->assertDatabaseHas('delivery_orders', [
            'id' => $deliveryOrder->id,
            'code' => $data['code'],
            'customer' => $data['customer'],
            'address' => $data['address'],
            'driver_name' => $data['driver_name'],
            'vehicle_plate' => $data['vehicle_plate'],
            'status' => $data['status'],
            'note' => $data['note'],
        ]);
    }

    public function test_can_partially_update_delivery_order(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a delivery order
        $deliveryOrder = DeliveryOrder::factory()->create();

        // Store original values
        $originalCode = $deliveryOrder->code;
        $originalCustomer = $deliveryOrder->customer;
        $originalStatus = $deliveryOrder->status;
        $originalNote = $deliveryOrder->note;

        // Prepare partial update data
        $data = [
            'date' => '2025-12-31 15:00:00',
            'driver_name' => 'Jane Doe',
            'status' => 'send',
        ];

        // Make the request
        $response = $this->patchJson("/api/delivery-orders/{$deliveryOrder->id}", $data);

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $deliveryOrder->id,
                     'code' => $originalCode, // Should remain unchanged
                     'customer' => $originalCustomer, // Should remain unchanged
                     'status' => $data['status'],
                     'driver_name' => $data['driver_name'],
                     'note' => $originalNote, // Should remain unchanged
                 ]);

        // Assert the record was partially updated in the database
        $this->assertDatabaseHas('delivery_orders', [
            'id' => $deliveryOrder->id,
            'code' => $originalCode,
            'customer' => $originalCustomer,
            'driver_name' => $data['driver_name'],
            'status' => $data['status'],
            'note' => $originalNote,
        ]);
    }

    public function test_can_delete_delivery_order(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a delivery order
        $deliveryOrder = DeliveryOrder::factory()->create();

        // Make the request
        $response = $this->deleteJson("/api/delivery-orders/{$deliveryOrder->id}");

        // Assert the response
        $response->assertStatus(204);

        // Assert the record was deleted from the database
        $this->assertDatabaseMissing('delivery_orders', [
            'id' => $deliveryOrder->id,
        ]);
    }

    public function test_can_create_delivery_order_with_valid_status(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Prepare data with valid status
        $data = [
            'code' => 'DO-TEST-12346',
            'date' => '2025-12-31 10:00:00',
            'customer' => 'Test Customer',
            'address' => '123 Test Street, Test City',
            'driver_name' => 'John Doe',
            'vehicle_plate' => 'ABC-1234',
            'status' => 'validated',
            'note' => 'Test note for validation',
        ];

        // Make the request
        $response = $this->postJson('/api/delivery-orders', $data);

        // Assert the response
        $response->assertStatus(201)
                 ->assertJson([
                     'code' => $data['code'],
                     'status' => $data['status'],
                     'note' => $data['note'],
                 ]);

        // Assert the record was created in the database
        $this->assertDatabaseHas('delivery_orders', [
            'code' => $data['code'],
            'status' => $data['status'],
            'note' => $data['note'],
        ]);
    }

    public function test_cannot_create_delivery_order_with_invalid_status(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Prepare data with invalid status
        $data = [
            'code' => 'DO-TEST-12347',
            'date' => '2025-12-31 10:00:00',
            'customer' => 'Test Customer',
            'address' => '123 Test Street, Test City',
            'driver_name' => 'John Doe',
            'vehicle_plate' => 'ABC-1234',
            'status' => 'invalid_status',
        ];

        // Make the request
        $response = $this->postJson('/api/delivery-orders', $data);

        // Assert the response
        $response->assertStatus(422)
                 ->assertJson([
                     'errors' => [
                         'status' => ['The selected status is invalid.'],
                     ]
                 ]);
    }

    public function test_unauthenticated_user_cannot_access_delivery_orders(): void
    {
        // Create a delivery order
        $deliveryOrder = DeliveryOrder::factory()->create();

        // Test index route
        $response = $this->getJson('/api/delivery-orders');
        $response->assertStatus(401); // Unauthenticated

        // Test show route
        $response = $this->getJson("/api/delivery-orders/{$deliveryOrder->id}");
        $response->assertStatus(401); // Unauthenticated

        // Test store route
        $response = $this->postJson('/api/delivery-orders', []);
        $response->assertStatus(401); // Unauthenticated

        // Test update route
        $response = $this->putJson("/api/delivery-orders/{$deliveryOrder->id}", []);
        $response->assertStatus(401); // Unauthenticated

        // Test delete route
        $response = $this->deleteJson("/api/delivery-orders/{$deliveryOrder->id}");
        $response->assertStatus(401); // Unauthenticated
    }
}