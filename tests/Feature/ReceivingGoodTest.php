<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ReceivingGood;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceivingGoodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate for protected routes
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_get_paginated_list_of_receiving_goods()
    {
        ReceivingGood::factory()->count(5)->create();

        $response = $this->getJson('/api/receiving-goods');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'current_page',
                    'data', // This is the paginated data array
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);
    }

    public function test_can_create_receiving_good()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $supplier = Supplier::factory()->create();

        $data = [
            'code' => 'RG-001',
            'po_id' => $purchaseOrder->id,
            'supplier_id' => $supplier->id,
            'date' => now()->toISOString(),
            'description' => 'Test receiving goods'
        ];

        $response = $this->postJson('/api/receiving-goods', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Good created successfully.'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'code',
                    'po_id',
                    'supplier_id',
                    'date',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('receiving_goods', [
            'code' => 'RG-001',
            'po_id' => $purchaseOrder->id,
            'supplier_id' => $supplier->id,
            'description' => 'Test receiving goods'
        ]);
    }

    public function test_can_show_single_receiving_good()
    {
        $receivingGood = ReceivingGood::factory()->create();

        $response = $this->getJson("/api/receiving-goods/{$receivingGood->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'code',
                    'po_id',
                    'supplier_id',
                    'date',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_can_update_receiving_good()
    {
        $receivingGood = ReceivingGood::factory()->create();
        $purchaseOrder = PurchaseOrder::factory()->create();
        $supplier = Supplier::factory()->create();

        $data = [
            'code' => 'RG-002',
            'po_id' => $purchaseOrder->id,
            'supplier_id' => $supplier->id,
            'date' => now()->toISOString(),
            'description' => 'Updated receiving goods'
        ];

        $response = $this->putJson("/api/receiving-goods/{$receivingGood->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Good updated successfully.'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'code',
                    'po_id',
                    'supplier_id',
                    'date',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('receiving_goods', [
            'id' => $receivingGood->id,
            'code' => 'RG-002',
            'po_id' => $purchaseOrder->id,
            'supplier_id' => $supplier->id,
            'description' => 'Updated receiving goods'
        ]);
    }

    public function test_can_delete_receiving_good()
    {
        $receivingGood = ReceivingGood::factory()->create();

        $response = $this->deleteJson("/api/receiving-goods/{$receivingGood->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Good deleted successfully.'
            ]);

        $this->assertDatabaseMissing('receiving_goods', [
            'id' => $receivingGood->id
        ]);
    }

    public function test_validation_on_create_receiving_good()
    {
        $response = $this->postJson('/api/receiving-goods', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error'
            ])
            ->assertJsonValidationErrors(['code', 'date']);
    }

    public function test_validation_on_update_receiving_good()
    {
        $receivingGood = ReceivingGood::factory()->create();

        $response = $this->putJson("/api/receiving-goods/{$receivingGood->id}", []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error'
            ])
            ->assertJsonValidationErrors(['code', 'date']);
    }
}