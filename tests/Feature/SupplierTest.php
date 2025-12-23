<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_create_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/suppliers', [
            'name' => 'Test Supplier',
            'contact' => '081234567890',
            'address' => 'Jl. Test No. 123, Jakarta',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'contact',
            'address',
            'created_at',
            'updated_at'
        ]);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Test Supplier',
            'contact' => '081234567890',
            'address' => 'Jl. Test No. 123, Jakarta',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_supplier(): void
    {
        $response = $this->postJson('/api/suppliers', [
            'name' => 'Test Supplier',
            'contact' => '081234567890',
            'address' => 'Jl. Test No. 123, Jakarta',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_get_all_suppliers(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Supplier::factory()->count(3)->create();

        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /** @test */
    public function authenticated_user_can_get_single_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $response = $this->getJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $supplier->id,
            'name' => $supplier->name,
            'contact' => $supplier->contact,
            'address' => $supplier->address,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_suppliers(): void
    {
        Supplier::factory()->create();

        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_update_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $response = $this->putJson("/api/suppliers/{$supplier->id}", [
            'name' => 'Updated Supplier Name',
            'contact' => '081234567891',
            'address' => 'Jl. Updated No. 456, Bandung',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
            'contact' => '081234567891',
            'address' => 'Jl. Updated No. 456, Bandung',
        ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
            'contact' => '081234567891',
            'address' => 'Jl. Updated No. 456, Bandung',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    /** @test */
    public function validation_fails_when_creating_supplier_without_required_fields(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/suppliers', [
            'name' => '', // Required field
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors'
        ]);
    }

    /** @test */
    public function validation_fails_when_updating_supplier_with_invalid_data(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $response = $this->putJson("/api/suppliers/{$supplier->id}", [
            'name' => '', // Required field
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors'
        ]);
    }

    /** @test */
    public function supplier_search_works_correctly(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Supplier::factory()->create([
            'name' => 'ABC Supplier',
            'contact' => '081234567890',
            'address' => 'Jl. Test ABC, Jakarta',
        ]);

        Supplier::factory()->create([
            'name' => 'XYZ Supplier',
            'contact' => '089876543210',
            'address' => 'Jl. Test XYZ, Bandung',
        ]);

        // Search for supplier with 'ABC' in name
        $response = $this->getJson('/api/suppliers?search=ABC');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'ABC Supplier']);
    }

    /** @test */
    public function supplier_filter_by_name_works_correctly(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Supplier::factory()->create([
            'name' => 'ABC Supplier',
            'contact' => '081234567890',
            'address' => 'Jl. Test ABC, Jakarta',
        ]);

        Supplier::factory()->create([
            'name' => 'XYZ Supplier',
            'contact' => '089876543210',
            'address' => 'Jl. Test XYZ, Bandung',
        ]);

        // Filter by name 'ABC'
        $response = $this->getJson('/api/suppliers?name=ABC');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'ABC Supplier']);
    }

    /** @test */
    public function supplier_filter_by_contact_works_correctly(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Supplier::factory()->create([
            'name' => 'ABC Supplier',
            'contact' => '081234567890',
            'address' => 'Jl. Test ABC, Jakarta',
        ]);

        Supplier::factory()->create([
            'name' => 'XYZ Supplier',
            'contact' => '089876543210',
            'address' => 'Jl. Test XYZ, Bandung',
        ]);

        // Filter by contact '081234567890'
        $response = $this->getJson('/api/suppliers?contact=081234567890');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['contact' => '081234567890']);
    }
}