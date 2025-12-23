<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PurchaseOrder;
use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and authenticate for the tests
        $this->actingAs(\App\Models\User::factory()->create());
    }

    /** @test */
    public function it_can_list_purchase_orders()
    {
        // Create some purchase orders
        $purchaseOrders = PurchaseOrder::factory(3)->create();

        $response = $this->getJson('/api/purchase-orders');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(3, 'data.data');
    }

    /** @test */
    public function it_can_show_a_single_purchase_order()
    {
        // Create a purchase order with related data
        $purchaseOrder = PurchaseOrder::factory()->create();

        $response = $this->getJson("/api/purchase-orders/{$purchaseOrder->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $purchaseOrder->id,
                         'code' => $purchaseOrder->code,
                         'rfq_id' => $purchaseOrder->rfq_id,
                         'supplier_id' => $purchaseOrder->supplier_id,
                         'date' => $purchaseOrder->date->toISOString(),
                         'description' => $purchaseOrder->description,
                         'grand_total' => $purchaseOrder->grand_total,
                         'status' => $purchaseOrder->status,
                     ]
                 ]);
    }

    /** @test */
    public function it_can_create_a_purchase_order()
    {
        $rfq = Rfq::factory()->create();
        $supplier = Supplier::factory()->create();
        
        $data = [
            'code' => 'PO-TEST-001',
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->id,
            'date' => now()->toISOString(),
            'description' => 'Test purchase order',
            'grand_total' => 1500.00,
            'status' => 'OPEN'
        ];

        $response = $this->postJson('/api/purchase-orders', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Purchase Order created successfully.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'code' => 'PO-TEST-001',
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->id,
            'description' => 'Test purchase order',
            'grand_total' => 1500.00,
            'status' => 'OPEN'
        ]);
    }

    /** @test */
    public function it_validates_purchase_order_creation()
    {
        $data = [
            'code' => '', // Required field
            'rfq_id' => 99999, // Non-existent RFQ
            'supplier_id' => 99999, // Non-existent supplier
            'date' => 'invalid-date', // Invalid date
            'description' => null,
            'grand_total' => -100, // Should be non-negative
            'status' => 'INVALID_STATUS' // Should be OPEN or RECEIVED
        ];

        $response = $this->postJson('/api/purchase-orders', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation error',
                 ]);
    }

    /** @test */
    public function it_can_update_a_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $rfq = Rfq::factory()->create();
        $supplier = Supplier::factory()->create();

        $data = [
            'code' => 'PO-UPDATED-001',
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->id,
            'date' => now()->toISOString(),
            'description' => 'Updated purchase order',
            'grand_total' => 2500.00,
            'status' => 'RECEIVED'
        ];

        $response = $this->putJson("/api/purchase-orders/{$purchaseOrder->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Purchase Order updated successfully.',
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'code' => 'PO-UPDATED-001',
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->id,
            'description' => 'Updated purchase order',
            'grand_total' => 2500.00,
            'status' => 'RECEIVED'
        ]);
    }

    /** @test */
    public function it_validates_purchase_order_update()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $data = [
            'code' => '', // Required field
            'rfq_id' => 99999, // Non-existent RFQ
            'supplier_id' => 99999, // Non-existent supplier
            'date' => 'invalid-date', // Invalid date
            'description' => null,
            'grand_total' => -100, // Should be non-negative
            'status' => 'INVALID_STATUS' // Should be OPEN or RECEIVED
        ];

        $response = $this->putJson("/api/purchase-orders/{$purchaseOrder->id}", $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation error',
                 ]);
    }

    /** @test */
    public function it_can_delete_a_purchase_order()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $response = $this->deleteJson("/api/purchase-orders/{$purchaseOrder->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Purchase Order deleted successfully.'
                 ]);

        $this->assertDatabaseMissing('purchase_orders', [
            'id' => $purchaseOrder->id
        ]);
    }

    /** @test */
    public function it_can_paginate_purchase_orders()
    {
        // Create more purchase orders than the default page size
        PurchaseOrder::factory(15)->create();

        $response = $this->getJson('/api/purchase-orders?per_page=10');

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
}