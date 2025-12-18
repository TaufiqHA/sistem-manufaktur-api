<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_projects()
    {
        Project::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_single_project()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'code' => $project->code,
                    'customer' => $project->customer,
                    'status' => $project->status,
                ]
            ]);
    }

    public function test_can_create_project()
    {
        $data = [
            'code' => 'TEST-001',
            'name' => 'Test Project',
            'customer' => 'Test Customer',
            'start_date' => '2024-01-01',
            'deadline' => '2024-06-01',
            'status' => 'PLANNED',
            'progress' => 0,
            'qty_per_unit' => 100,
            'procurement_qty' => 1000,
            'total_qty' => 10000,
            'unit' => 'PCS',
            'is_locked' => false,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'code' => 'TEST-001',
            'name' => 'Test Project',
            'customer' => 'Test Customer',
        ]);
    }

    public function test_cannot_create_project_with_invalid_data()
    {
        $data = [
            'code' => '', // Required field
            'name' => '', // Required field
            'customer' => '', // Required field
            'start_date' => 'invalid-date',
            'deadline' => '2023-01-01', // Should be after start date
            'status' => 'INVALID_STATUS', // Invalid status
            'progress' => 150, // Should be between 0-100
            'qty_per_unit' => -1, // Should be positive
            'procurement_qty' => -1, // Should be positive
            'total_qty' => -1, // Should be positive
            'unit' => str_repeat('A', 256), // Too long
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/projects', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'code', 'name', 'customer', 'start_date',
                'status', 'progress', 'qty_per_unit',
                'procurement_qty', 'total_qty', 'unit'
            ]);
    }

    public function test_can_update_project()
    {
        $project = Project::factory()->create();
        
        $data = [
            'code' => 'UPDATED-001',
            'name' => 'Updated Project Name',
            'customer' => 'Updated Customer',
            'start_date' => '2024-02-01',
            'deadline' => '2024-07-01',
            'status' => 'IN_PROGRESS',
            'progress' => 25,
            'qty_per_unit' => 200,
            'procurement_qty' => 2000,
            'total_qty' => 20000,
            'unit' => 'SET',
            'is_locked' => true,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$project->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'code' => 'UPDATED-001',
            'name' => 'Updated Project Name',
            'customer' => 'Updated Customer',
            'status' => 'IN_PROGRESS',
            'progress' => 25,
        ]);
    }

    public function test_cannot_update_project_with_invalid_data()
    {
        $project = Project::factory()->create();

        $data = [
            'code' => '', // Required field
            'name' => '', // Required field
            'customer' => '', // Required field
            'start_date' => 'invalid-date',
            'deadline' => '2023-01-01', // Should be after start date
            'status' => 'INVALID_STATUS', // Invalid status
            'progress' => 150, // Should be between 0-100
            'qty_per_unit' => -1, // Should be positive
            'procurement_qty' => -1, // Should be positive
            'total_qty' => -1, // Should be positive
            'unit' => str_repeat('A', 256), // Too long
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/projects/{$project->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'code', 'name', 'customer', 'start_date',
                'status', 'progress', 'qty_per_unit',
                'procurement_qty', 'total_qty', 'unit'
            ]);
    }

    public function test_can_delete_project()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_unauthorized_user_cannot_access_projects()
    {
        $response = $this->getJson('/api/projects');
        $response->assertUnauthorized();

        $response = $this->postJson('/api/projects', []);
        $response->assertUnauthorized();

        $project = Project::factory()->create();
        $response = $this->getJson("/api/projects/{$project->id}");
        $response->assertUnauthorized();

        $response = $this->putJson("/api/projects/{$project->id}", []);
        $response->assertUnauthorized();

        $response = $this->deleteJson("/api/projects/{$project->id}");
        $response->assertUnauthorized();
    }
}