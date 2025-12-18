<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MachineTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function it_can_create_a_machine()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/machines', [
                'code' => 'MCH-TEST-1234',
                'name' => 'Testing Machine',
                'type' => 'PRESS',
                'capacity_per_hour' => 100,
                'status' => 'IDLE',
                'personnel' => [['id' => '1', 'name' => 'John Doe', 'position' => 'Operator']],
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Machine created successfully',
            ]);

        $this->assertDatabaseHas('machines', [
            'code' => 'MCH-TEST-1234',
            'name' => 'Testing Machine',
            'type' => 'PRESS',
            'capacity_per_hour' => 100,
            'status' => 'IDLE',
        ]);
    }

    /** @test */
    public function it_validates_machine_creation()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/machines', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'code',
                    'name',
                    'type',
                    'capacity_per_hour',
                    'status',
                    'personnel'
                ]
            ])
            ->assertJsonFragment([
                'code' => [
                    'The code field is required.'
                ]
            ]);
    }

    /** @test */
    public function it_can_get_all_machines()
    {
        Machine::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/machines');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_get_a_single_machine()
    {
        $machine = Machine::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/machines/{$machine->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $machine->id,
                    'code' => $machine->code,
                    'name' => $machine->name,
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_machine()
    {
        $machine = Machine::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/machines/{$machine->id}", [
                'name' => 'Updated Machine Name',
                'status' => 'RUNNING',
                'capacity_per_hour' => 150,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Machine updated successfully',
            ]);

        $this->assertDatabaseHas('machines', [
            'id' => $machine->id,
            'name' => 'Updated Machine Name',
            'status' => 'RUNNING',
            'capacity_per_hour' => 150,
        ]);
    }

    /** @test */
    public function it_can_delete_a_machine()
    {
        $machine = Machine::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/machines/{$machine->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Machine deleted successfully',
            ]);

        $this->assertDatabaseMissing('machines', [
            'id' => $machine->id,
        ]);
    }

    /** @test */
    public function it_can_toggle_machine_maintenance_status()
    {
        $machine = Machine::factory()->create([
            'is_maintenance' => false,
            'status' => 'IDLE',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/machines/{$machine->id}/toggle-maintenance");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Maintenance status updated successfully',
            ]);

        $this->assertDatabaseHas('machines', [
            'id' => $machine->id,
            'is_maintenance' => true,
            'status' => 'MAINTENANCE',
        ]);
    }

    /** @test */
    public function it_can_get_machines_by_type()
    {
        Machine::factory()->count(2)->create(['type' => 'PRESS']);
        Machine::factory()->count(1)->create(['type' => 'LAS']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/machines/type/PRESS');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_get_machines_by_status()
    {
        Machine::factory()->count(3)->create(['status' => 'IDLE']);
        Machine::factory()->count(1)->create(['status' => 'RUNNING']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/machines/status/IDLE');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_handles_invalid_machine_type_request()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/machines/type/INVALID_TYPE');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid machine type',
            ]);
    }

    /** @test */
    public function it_handles_invalid_machine_status_request()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/machines/status/INVALID_STATUS');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid machine status',
            ]);
    }

    /** @test */
    public function it_requires_authentication_for_machine_routes()
    {
        $response = $this->getJson('/api/machines');
        $response->assertUnauthorized();

        $response = $this->postJson('/api/machines', []);
        $response->assertUnauthorized();

        $machine = Machine::factory()->create();
        $response = $this->getJson("/api/machines/{$machine->id}");
        $response->assertUnauthorized();
    }
}