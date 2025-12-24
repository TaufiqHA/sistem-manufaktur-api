<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ReceivingItem;
use App\Models\ReceivingGood;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceivingItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate for protected routes
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_get_paginated_list_of_receiving_items()
    {
        ReceivingItem::factory()->count(5)->create();

        $response = $this->getJson('/api/receiving-items');

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

    public function test_can_create_receiving_item()
    {
        $receivingGood = ReceivingGood::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'receiving_id' => $receivingGood->id,
            'material_id' => $material->id,
            'name' => 'Test Receiving Item',
            'qty' => 10
        ];

        $response = $this->postJson('/api/receiving-items', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Item created successfully.'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'receiving_id',
                    'material_id',
                    'name',
                    'qty',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('receiving_items', [
            'receiving_id' => $receivingGood->id,
            'material_id' => $material->id,
            'name' => 'Test Receiving Item',
            'qty' => 10
        ]);
    }

    public function test_can_show_single_receiving_item()
    {
        $receivingItem = ReceivingItem::factory()->create();

        $response = $this->getJson("/api/receiving-items/{$receivingItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'receiving_id',
                    'material_id',
                    'name',
                    'qty',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_can_update_receiving_item()
    {
        $receivingItem = ReceivingItem::factory()->create();
        $receivingGood = ReceivingGood::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'receiving_id' => $receivingGood->id,
            'material_id' => $material->id,
            'name' => 'Updated Receiving Item',
            'qty' => 20
        ];

        $response = $this->putJson("/api/receiving-items/{$receivingItem->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Item updated successfully.'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'receiving_id',
                    'material_id',
                    'name',
                    'qty',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('receiving_items', [
            'id' => $receivingItem->id,
            'receiving_id' => $receivingGood->id,
            'material_id' => $material->id,
            'name' => 'Updated Receiving Item',
            'qty' => 20
        ]);
    }

    public function test_can_delete_receiving_item()
    {
        $receivingItem = ReceivingItem::factory()->create();

        $response = $this->deleteJson("/api/receiving-items/{$receivingItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Item deleted successfully.'
            ]);

        $this->assertDatabaseMissing('receiving_items', [
            'id' => $receivingItem->id
        ]);
    }

    public function test_validation_on_create_receiving_item()
    {
        $response = $this->postJson('/api/receiving-items', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error'
            ])
            ->assertJsonValidationErrors(['name', 'qty']);
    }

    public function test_validation_on_update_receiving_item()
    {
        $receivingItem = ReceivingItem::factory()->create();

        $response = $this->putJson("/api/receiving-items/{$receivingItem->id}", []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error'
            ])
            ->assertJsonValidationErrors(['name', 'qty']);
    }

    public function test_can_get_receiving_items_by_receiving()
    {
        $receivingGood = ReceivingGood::factory()->create();
        $receivingItems = ReceivingItem::factory()->count(3)->create([
            'receiving_id' => $receivingGood->id
        ]);

        $response = $this->getJson("/api/receiving-goods/{$receivingGood->id}/items");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'receiving_id',
                        'material_id',
                        'name',
                        'qty',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $this->assertCount(3, $response->json('data'));
    }
}
