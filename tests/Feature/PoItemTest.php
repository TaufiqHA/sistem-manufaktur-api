<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PoItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and authenticate for the tests
        $this->user = $this->createUser();
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Helper method to create a test user
     */
    private function createUser()
    {
        return \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function it_can_list_po_items()
    {
        // Create some PoItems
        $poItems = PoItem::factory()->count(3)->create();

        $response = $this->getJson('/api/po-items');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(3, 'data.data');
    }

    /** @test */
    public function it_can_show_a_po_item()
    {
        $poItem = PoItem::factory()->create();

        $response = $this->getJson("/api/po-items/{$poItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $poItem->id,
                         'name' => $poItem->name,
                         'qty' => $poItem->qty,
                         'price' => $poItem->price,
                         'subtotal' => $poItem->subtotal,
                     ]
                 ]);
    }

    /** @test */
    public function it_can_create_a_po_item()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'po_id' => $purchaseOrder->id,
            'material_id' => $material->id,
            'name' => 'Test PO Item',
            'qty' => 10,
            'price' => 100.00,
            'subtotal' => 1000.00,
        ];

        $response = $this->postJson('/api/po-items', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'PO Item created successfully.',
                     'data' => [
                         'name' => $data['name'],
                         'qty' => $data['qty'],
                         'price' => $data['price'],
                         'subtotal' => $data['subtotal'],
                     ]
                 ]);

        $this->assertDatabaseHas('po_items', [
            'name' => $data['name'],
            'qty' => $data['qty'],
            'price' => $data['price'],
            'subtotal' => $data['subtotal'],
        ]);
    }

    /** @test */
    public function it_validates_po_item_creation()
    {
        $response = $this->postJson('/api/po-items', []);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation error',
                 ]);
    }

    /** @test */
    public function it_can_update_a_po_item()
    {
        $poItem = PoItem::factory()->create();
        $purchaseOrder = PurchaseOrder::factory()->create();
        $material = Material::factory()->create();

        $updatedData = [
            'po_id' => $purchaseOrder->id,
            'material_id' => $material->id,
            'name' => 'Updated PO Item',
            'qty' => 20,
            'price' => 200.00,
            'subtotal' => 4000.00,
        ];

        $response = $this->putJson("/api/po-items/{$poItem->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'PO Item updated successfully.',
                     'data' => [
                         'id' => $poItem->id,
                         'name' => $updatedData['name'],
                         'qty' => $updatedData['qty'],
                         'price' => $updatedData['price'],
                         'subtotal' => $updatedData['subtotal'],
                     ]
                 ]);

        $this->assertDatabaseHas('po_items', [
            'id' => $poItem->id,
            'name' => $updatedData['name'],
            'qty' => $updatedData['qty'],
            'price' => $updatedData['price'],
            'subtotal' => $updatedData['subtotal'],
        ]);
    }

    /** @test */
    public function it_can_delete_a_po_item()
    {
        $poItem = PoItem::factory()->create();

        $response = $this->deleteJson("/api/po-items/{$poItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'PO Item deleted successfully.'
                 ]);

        $this->assertDatabaseMissing('po_items', [
            'id' => $poItem->id,
        ]);
    }

    /** @test */
    public function it_can_search_po_items()
    {
        $poItem = PoItem::factory()->create(['name' => 'Special Item']);
        PoItem::factory()->count(2)->create();

        $response = $this->getJson('/api/po-items?search=Special');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(1, 'data.data');
    }

    /** @test */
    public function it_can_filter_po_items_by_po_id()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $otherPurchaseOrder = PurchaseOrder::factory()->create();
        
        $poItem1 = PoItem::factory()->create(['po_id' => $purchaseOrder->id]);
        $poItem2 = PoItem::factory()->create(['po_id' => $otherPurchaseOrder->id]);

        $response = $this->getJson("/api/po-items?po_id={$purchaseOrder->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(1, 'data.data');
    }

    /** @test */
    public function it_can_filter_po_items_by_material_id()
    {
        $material = Material::factory()->create();
        $otherMaterial = Material::factory()->create();
        
        $poItem1 = PoItem::factory()->create(['material_id' => $material->id]);
        $poItem2 = PoItem::factory()->create(['material_id' => $otherMaterial->id]);

        $response = $this->getJson("/api/po-items?material_id={$material->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(1, 'data.data');
    }

    /** @test */
    public function it_can_filter_po_items_by_quantity_range()
    {
        $poItem1 = PoItem::factory()->create(['qty' => 5]);
        $poItem2 = PoItem::factory()->create(['qty' => 15]);
        $poItem3 = PoItem::factory()->create(['qty' => 25]);

        $response = $this->getJson('/api/po-items?min_qty=10&max_qty=20');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(1, 'data.data'); // Should only return poItem2 with qty=15
    }

    /** @test */
    public function it_can_filter_po_items_by_price_range()
    {
        $poItem1 = PoItem::factory()->create(['price' => 50.00]);
        $poItem2 = PoItem::factory()->create(['price' => 150.00]);
        $poItem3 = PoItem::factory()->create(['price' => 250.00]);

        $response = $this->getJson('/api/po-items?min_price=100&max_price=200');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonCount(1, 'data.data'); // Should only return poItem2 with price=150.00
    }
}