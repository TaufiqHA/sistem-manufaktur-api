<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
        
        // Seed any necessary data
        Artisan::call('db:seed');
    }

    /** @test */
    public function it_can_list_materials()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create some materials
        Material::factory()->count(5)->create();

        $response = $this->getJson('/api/materials');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data', // paginated data array
                         'current_page',
                         'first_page_url',
                         'from',
                         'last_page',
                         'last_page_url',
                         'links' => [
                             '*' => [
                                 'url',
                                 'label',
                                 'active',
                                 'page'
                             ]
                         ],
                         'next_page_url',
                         'path',
                         'per_page',
                         'prev_page_url',
                         'to',
                         'total'
                     ]
                 ])
                 ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'code' => 'MAT-TEST-001',
            'name' => 'Test Material',
            'unit' => 'kg',
            'current_stock' => 100,
            'safety_stock' => 10,
            'price_per_unit' => 25.50,
            'category' => 'RAW'
        ];

        $response = $this->postJson('/api/materials', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Material created successfully',
                     'data' => [
                         'code' => $data['code'],
                         'name' => $data['name'],
                         'unit' => $data['unit'],
                         'current_stock' => $data['current_stock'],
                         'safety_stock' => $data['safety_stock'],
                         'price_per_unit' => $data['price_per_unit'],
                         'category' => $data['category']
                     ]
                 ]);

        $this->assertDatabaseHas('materials', [
            'code' => $data['code'],
            'name' => $data['name'],
            'unit' => $data['unit'],
            'current_stock' => $data['current_stock'],
            'safety_stock' => $data['safety_stock'],
            'price_per_unit' => $data['price_per_unit'],
            'category' => $data['category']
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/materials', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'code',
                     'name',
                     'unit',
                     'current_stock',
                     'safety_stock',
                     'price_per_unit',
                     'category'
                 ]);
    }

    /** @test */
    public function it_requires_unique_code_when_creating_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $existingMaterial = Material::factory()->create(['code' => 'EXISTING-CODE']);

        $data = [
            'code' => 'EXISTING-CODE',
            'name' => 'Another Material',
            'unit' => 'kg',
            'current_stock' => 50,
            'safety_stock' => 5,
            'price_per_unit' => 15.25,
            'category' => 'FINISHING'
        ];

        $response = $this->postJson('/api/materials', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code']);
    }

    /** @test */
    public function it_can_show_a_single_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create();

        $response = $this->getJson("/api/materials/{$material->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $material->id,
                         'code' => $material->code,
                         'name' => $material->name,
                         'unit' => $material->unit,
                         'current_stock' => $material->current_stock,
                         'safety_stock' => $material->safety_stock,
                         'price_per_unit' => $material->price_per_unit,
                         'category' => $material->category
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create();

        $updateData = [
            'code' => 'UPDATED-CODE-001',
            'name' => 'Updated Material Name',
            'unit' => 'pcs',
            'current_stock' => 200,
            'safety_stock' => 20,
            'price_per_unit' => 30.75,
            'category' => 'HARDWARE'
        ];

        $response = $this->putJson("/api/materials/{$material->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Material updated successfully',
                     'data' => [
                         'id' => $material->id,
                         'code' => $updateData['code'],
                         'name' => $updateData['name'],
                         'unit' => $updateData['unit'],
                         'current_stock' => $updateData['current_stock'],
                         'safety_stock' => $updateData['safety_stock'],
                         'price_per_unit' => $updateData['price_per_unit'],
                         'category' => $updateData['category']
                     ]
                 ]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'code' => $updateData['code'],
            'name' => $updateData['name'],
            'unit' => $updateData['unit'],
            'current_stock' => $updateData['current_stock'],
            'safety_stock' => $updateData['safety_stock'],
            'price_per_unit' => $updateData['price_per_unit'],
            'category' => $updateData['category']
        ]);
    }

    /** @test */
    public function it_can_delete_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create();

        $response = $this->deleteJson("/api/materials/{$material->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Material deleted successfully'
                 ]);

        $this->assertSoftDeleted('materials', [
            'id' => $material->id
        ]);
    }

    /** @test */
    public function it_can_update_material_stock_by_adding()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create([
            'current_stock' => 50,
        ]);

        $response = $this->patchJson("/api/materials/{$material->id}/stock", [
            'stock_change' => 25,
            'operation' => 'add'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Stock updated successfully',
                     'data' => [
                         'id' => $material->id,
                         'current_stock' => 75 // 50 + 25
                     ]
                 ]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'current_stock' => 75
        ]);
    }

    /** @test */
    public function it_can_update_material_stock_by_reducing()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create([
            'current_stock' => 50,
        ]);

        $response = $this->patchJson("/api/materials/{$material->id}/stock", [
            'stock_change' => 20,
            'operation' => 'reduce'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Stock updated successfully',
                     'data' => [
                         'id' => $material->id,
                         'current_stock' => 30 // 50 - 20
                     ]
                 ]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'current_stock' => 30
        ]);
    }

    /** @test */
    public function it_prevents_reducing_stock_below_zero()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create([
            'current_stock' => 10,
        ]);

        $response = $this->patchJson("/api/materials/{$material->id}/stock", [
            'stock_change' => 15, // More than current stock
            'operation' => 'reduce'
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Not enough stock to reduce'
                 ]);
    }

    /** @test */
    public function it_can_get_materials_with_low_stock()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create materials with low stock (current < safety)
        $lowStockMaterial = Material::factory()->create([
            'current_stock' => 5,
            'safety_stock' => 10
        ]);

        // Create materials with sufficient stock
        Material::factory()->create([
            'current_stock' => 50,
            'safety_stock' => 10
        ]);

        $response = $this->getJson('/api/materials-low-stock');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        // Should return low stock materials (the controller returns a collection, not paginated)
        $responseData = collect($response->json('data'));
        $this->assertTrue($responseData->contains('id', $lowStockMaterial->id));
    }

    /** @test */
    public function it_can_search_materials()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create materials
        $material1 = Material::factory()->create([
            'name' => 'Wood Planks',
            'code' => 'MAT-WOOD-001'
        ]);

        $material2 = Material::factory()->create([
            'name' => 'Steel Rods',
            'code' => 'MAT-STEEL-001'
        ]);

        $response = $this->getJson('/api/materials?search=Wood');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data', // paginated data array
                         'current_page',
                         'first_page_url',
                         'from',
                         'last_page',
                         'last_page_url',
                         'links' => [
                             '*' => [
                                 'url',
                                 'label',
                                 'active',
                                 'page'
                             ]
                         ],
                         'next_page_url',
                         'path',
                         'per_page',
                         'prev_page_url',
                         'to',
                         'total'
                     ]
                 ])
                 ->assertJson(['success' => true]);

        $responseData = $response->json('data.data');
        $this->assertCount(1, $responseData);
        $this->assertEquals($material1->id, $responseData[0]['id']);
    }

    /** @test */
    public function it_filters_materials_by_category()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create materials in different categories
        $rawMaterial = Material::factory()->create(['category' => 'RAW']);
        Material::factory()->create(['category' => 'FINISHING']);

        $response = $this->getJson('/api/materials?category=RAW');

        $response->assertStatus(200);

        $responseData = $response->json('data.data');
        $this->assertCount(1, $responseData);
        $this->assertEquals($rawMaterial->id, $responseData[0]['id']);
    }

    /** @test */
    public function it_filters_materials_by_low_stock_status()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create low stock material
        $lowStockMaterial = Material::factory()->create([
            'current_stock' => 5,
            'safety_stock' => 10
        ]);

        // Create sufficient stock material
        Material::factory()->create([
            'current_stock' => 50,
            'safety_stock' => 10
        ]);

        $response = $this->getJson('/api/materials?low_stock=true');

        $response->assertStatus(200);

        $responseData = $response->json('data.data');
        $this->assertCount(1, $responseData);
        $this->assertEquals($lowStockMaterial->id, $responseData[0]['id']);
    }

    /** @test */
    public function it_requires_authentication_for_material_routes()
    {
        // Test without authentication
        $response = $this->getJson('/api/materials');

        $response->assertStatus(401); // Unauthorized

        // Test create without auth
        $response = $this->postJson('/api/materials', [
            'code' => 'TEST-CODE',
            'name' => 'Test Material',
            'unit' => 'kg',
            'current_stock' => 100,
            'safety_stock' => 10,
            'price_per_unit' => 10.50,
            'category' => 'RAW'
        ]);

        $response->assertStatus(401);
    }
}