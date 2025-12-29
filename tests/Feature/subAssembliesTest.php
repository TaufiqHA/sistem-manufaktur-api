<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\SubAssemblies;
use App\Models\Material;
use App\Models\User;

class subAssembliesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_sub_assemblies()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create some sub assemblies
        $subAssemblies = SubAssemblies::factory()->count(3)->create();

        // Make the request
        $response = $this->getJson('/api/sub-assemblies');

        // Assert the response
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertArrayHasKey('data', $responseData);
        $this->assertCount(3, $responseData['data']);
    }

    public function test_can_get_single_sub_assembly()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a sub assembly
        $subAssembly = SubAssemblies::factory()->create();

        // Make the request
        $response = $this->getJson("/api/sub-assemblies/{$subAssembly->id}");

        // Assert the response
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertEquals($subAssembly->id, $responseData['id']);
        $this->assertEquals($subAssembly->item_id, $responseData['item_id']);
        $this->assertEquals($subAssembly->name, $responseData['name']);
    }

    public function test_can_create_sub_assembly()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a material for the foreign key
        $material = Material::factory()->create();

        // Prepare the data
        $data = [
            'item_id' => 'ITEM001',
            'name' => 'Test Sub Assembly',
            'qty_per_parent' => 2,
            'material_id' => $material->id,
            'processes' => json_encode(['process_1' => 'cutting', 'process_2' => 'drilling']),
            'total_needed' => 100,
            'completed_qty' => 0,
            'total_produced' => 0,
            'consumed_qty' => 0,
            'step_stats' => json_encode(['step_1' => ['completed' => false, 'progress' => 0]]),
            'is_locked' => false,
        ];

        // Make the request
        $response = $this->postJson('/api/sub-assemblies', $data);

        // Assert the response
        $response->assertStatus(201)
                 ->assertJson([
                     'item_id' => $data['item_id'],
                     'name' => $data['name'],
                     'qty_per_parent' => $data['qty_per_parent'],
                     'material_id' => $data['material_id'],
                     'total_needed' => $data['total_needed'],
                 ]);

        // Assert the sub assembly was created in the database
        $this->assertDatabaseHas('sub_assemblies', [
            'item_id' => $data['item_id'],
            'name' => $data['name'],
        ]);
    }

    public function test_can_update_sub_assembly()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a sub assembly and material
        $subAssembly = SubAssemblies::factory()->create();
        $material = Material::factory()->create();

        // Prepare the update data
        $data = [
            'name' => 'Updated Sub Assembly Name',
            'qty_per_parent' => 5,
            'material_id' => $material->id,
        ];

        // Make the request
        $response = $this->putJson("/api/sub-assemblies/{$subAssembly->id}", $data);

        // Assert the response
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertEquals($subAssembly->id, $responseData['id']);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertEquals($data['qty_per_parent'], $responseData['qty_per_parent']);

        // Assert the sub assembly was updated in the database
        $this->assertDatabaseHas('sub_assemblies', [
            'id' => $subAssembly->id,
            'name' => $data['name'],
        ]);
    }

    public function test_can_delete_sub_assembly()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a sub assembly
        $subAssembly = SubAssemblies::factory()->create();

        // Make the request
        $response = $this->deleteJson("/api/sub-assemblies/{$subAssembly->id}");

        // Check if response is 204 (no content) or 200 with empty body
        $response->assertStatus(204);

        // Verify the sub assembly was deleted from the database
        $this->assertDatabaseMissing('sub_assemblies', [
            'id' => $subAssembly->id,
        ]);
    }

    public function test_validation_for_creating_sub_assembly()
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Prepare invalid data
        $data = [
            'name' => '', // Required field is empty
            'qty_per_parent' => -1, // Should be >= 0
        ];

        // Make the request
        $response = $this->postJson('/api/sub-assemblies', $data);

        // Assert the response
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'name',
                'qty_per_parent'
            ]
        ]);
    }

    public function test_unauthorized_access_to_sub_assemblies()
    {
        // Make the request without authentication
        $response = $this->getJson('/api/sub-assemblies');

        // Assert the response
        $response->assertStatus(401);
    }
}
