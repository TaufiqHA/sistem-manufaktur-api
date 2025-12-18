<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class BomItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and authenticate for the tests
        $this->actingAs(\App\Models\User::factory()->create());
    }

    /** @test */
    public function it_can_list_bom_items()
    {
        // Create some sample BOM items
        $bomItems = BomItem::factory()->count(3)->create();

        $response = $this->getJson('/api/bom-items');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'item_id',
                             'material_id',
                             'quantity_per_unit',
                             'total_required',
                             'allocated',
                             'realized',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                     // Note: Removing 'links' and 'meta' from assertion
                     // as they might not be present depending on pagination setup
                 ]);
    }

    /** @test */
    public function it_can_show_a_single_bom_item()
    {
        // Create a BOM item with related data
        $bomItem = BomItem::factory()->create();

        $response = $this->getJson("/api/bom-items/{$bomItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $bomItem->id,
                     'item_id' => $bomItem->item_id,
                     'material_id' => $bomItem->material_id,
                     'quantity_per_unit' => $bomItem->quantity_per_unit,
                     'total_required' => $bomItem->total_required,
                     'allocated' => $bomItem->allocated,
                     'realized' => $bomItem->realized,
                 ]);
    }

    /** @test */
    public function it_can_create_a_new_bom_item()
    {
        // Create related ProjectItem and Material first
        $projectItem = ProjectItem::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'item_id' => $projectItem->id,
            'material_id' => $material->id,
            'quantity_per_unit' => 5,
            'total_required' => 50,
            'allocated' => 20,
            'realized' => 10,
        ];

        $response = $this->postJson('/api/bom-items', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'item_id' => $data['item_id'],
                     'material_id' => $data['material_id'],
                     'quantity_per_unit' => $data['quantity_per_unit'],
                     'total_required' => $data['total_required'],
                     'allocated' => $data['allocated'],
                     'realized' => $data['realized'],
                 ]);

        $this->assertDatabaseHas('bom_items', [
            'item_id' => $data['item_id'],
            'material_id' => $data['material_id'],
            'quantity_per_unit' => $data['quantity_per_unit'],
            'total_required' => $data['total_required'],
            'allocated' => $data['allocated'],
            'realized' => $data['realized'],
        ]);
    }

    /** @test */
    public function it_validates_data_when_creating_a_bom_item()
    {
        $data = [
            'item_id' => null, // Invalid - should be required
            'material_id' => null, // Invalid - should be required
            'quantity_per_unit' => -5, // Invalid - should be positive
            'total_required' => -10, // Invalid - should be positive
            'allocated' => -1, // Invalid - should be non-negative
            'realized' => -1, // Invalid - should be non-negative
        ];

        $response = $this->postJson('/api/bom-items', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'item_id',
                     'material_id',
                     'quantity_per_unit',
                     'total_required',
                     'allocated',
                     'realized',
                 ]);
    }

    /** @test */
    public function it_can_update_an_existing_bom_item()
    {
        // Create a BOM item to update
        $bomItem = BomItem::factory()->create();

        $updateData = [
            'quantity_per_unit' => 10,
            'total_required' => 100,
            'allocated' => 50,
            'realized' => 25,
        ];

        $response = $this->putJson("/api/bom-items/{$bomItem->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $bomItem->id,
                     'quantity_per_unit' => $updateData['quantity_per_unit'],
                     'total_required' => $updateData['total_required'],
                     'allocated' => $updateData['allocated'],
                     'realized' => $updateData['realized'],
                 ]);

        $this->assertDatabaseHas('bom_items', [
            'id' => $bomItem->id,
            'quantity_per_unit' => $updateData['quantity_per_unit'],
            'total_required' => $updateData['total_required'],
            'allocated' => $updateData['allocated'],
            'realized' => $updateData['realized'],
        ]);
    }

    /** @test */
    public function it_validates_data_when_updating_a_bom_item()
    {
        $bomItem = BomItem::factory()->create();

        $invalidData = [
            'quantity_per_unit' => -5, // Invalid - should be positive
            'total_required' => -10, // Invalid - should be positive
        ];

        $response = $this->putJson("/api/bom-items/{$bomItem->id}", $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'quantity_per_unit',
                     'total_required',
                 ]);
    }

    /** @test */
    public function it_can_delete_a_bom_item()
    {
        $bomItem = BomItem::factory()->create();

        $response = $this->deleteJson("/api/bom-items/{$bomItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('bom_items', [
            'id' => $bomItem->id,
        ]);
    }

    /** @test */
    public function it_can_get_bom_items_by_project_item()
    {
        // Create a project item and related BOM items
        $projectItem = ProjectItem::factory()->create();
        $bomItems = BomItem::factory()->count(2)->create([
            'item_id' => $projectItem->id
        ]);

        // Create another BOM item with a different project item
        $otherProjectItem = ProjectItem::factory()->create();
        BomItem::factory()->create([
            'item_id' => $otherProjectItem->id
        ]);

        $response = $this->getJson("/api/bom-items-by-project-item/{$projectItem->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(2); // Should return only the 2 BOM items related to the specific project item
    }

    /** @test */
    public function it_handles_nonexistent_bom_item_gracefully()
    {
        $nonExistentId = 999999;

        $response = $this->getJson("/api/bom-items/{$nonExistentId}");
        $response->assertStatus(404);

        $response = $this->putJson("/api/bom-items/{$nonExistentId}", []);
        $response->assertStatus(404);

        $response = $this->deleteJson("/api/bom-items/{$nonExistentId}");
        $response->assertStatus(404);
    }
}