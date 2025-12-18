<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and authenticate for protected routes
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_get_all_tasks(): void
    {
        // Create sample tasks
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'current_page',
                         'data' => [
                             '*' => [
                                 'id',
                                 'project_id',
                                 'project_name',
                                 'item_id',
                                 'item_name',
                                 'step',
                                 'machine_id',
                                 'target_qty',
                                 'completed_qty',
                                 'defect_qty',
                                 'status',
                                 'downtime_start',
                                 'total_downtime_minutes',
                                 'created_at',
                                 'updated_at',
                                 'project',
                                 'project_item',
                                 'machine'
                             ]
                         ],
                         'first_page_url',
                         'from',
                         'last_page',
                         'last_page_url',
                         'links' => [
                             '*' => [
                                 'url',
                                 'label',
                                 'page',
                                 'active'
                             ]
                         ],
                         'next_page_url',
                         'path',
                         'per_page',
                         'prev_page_url',
                         'to',
                         'total'
                     ]
                 ]);
    }

    public function test_can_create_task(): void
    {
        $project = Project::factory()->create();
        $projectItem = ProjectItem::factory()->create();
        $machine = Machine::factory()->create();

        $data = [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $projectItem->id,
            'item_name' => $projectItem->name,
            'step' => 'Assembly',
            'machine_id' => $machine->id,
            'target_qty' => 100,
            'status' => 'PENDING',
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task created successfully.',
                     'data' => [
                         'project_id' => $data['project_id'],
                         'project_name' => $data['project_name'],
                         'item_id' => $data['item_id'],
                         'item_name' => $data['item_name'],
                         'step' => $data['step'],
                         'machine_id' => $data['machine_id'],
                         'target_qty' => $data['target_qty'],
                         'status' => $data['status'],
                         'completed_qty' => 0,
                         'defect_qty' => 0,
                         'total_downtime_minutes' => 0,
                     ]
                 ]);

        $this->assertDatabaseHas('tasks', [
            'project_id' => $data['project_id'],
            'project_name' => $data['project_name'],
            'item_id' => $data['item_id'],
            'item_name' => $data['item_name'],
            'step' => $data['step'],
            'machine_id' => $data['machine_id'],
            'target_qty' => $data['target_qty'],
            'status' => $data['status'],
        ]);
    }

    public function test_can_show_single_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $task->id,
                         'project_id' => $task->project_id,
                         'project_name' => $task->project_name,
                         'item_id' => $task->item_id,
                         'item_name' => $task->item_name,
                         'step' => $task->step,
                         'machine_id' => $task->machine_id,
                         'target_qty' => $task->target_qty,
                         'completed_qty' => $task->completed_qty,
                         'defect_qty' => $task->defect_qty,
                         'status' => $task->status,
                     ]
                 ]);
    }

    public function test_returns_404_when_task_not_found(): void
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Task not found.',
                 ]);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create();

        $updateData = [
            'status' => 'IN_PROGRESS',
            'target_qty' => 200,
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task updated successfully.',
                     'data' => [
                         'id' => $task->id,
                         'status' => $updateData['status'],
                         'target_qty' => $updateData['target_qty'],
                     ]
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => $updateData['status'],
            'target_qty' => $updateData['target_qty'],
        ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task deleted successfully.',
                 ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_can_update_task_status(): void
    {
        $task = Task::factory()->create();

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'IN_PROGRESS'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task status updated successfully.',
                     'data' => [
                         'id' => $task->id,
                         'status' => 'IN_PROGRESS',
                     ]
                 ]);

        $task->refresh();
        $this->assertEquals('IN_PROGRESS', $task->status);
    }

    public function test_can_update_task_quantities(): void
    {
        $task = Task::factory()->create([
            'target_qty' => 100
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/quantities", [
            'completed_qty' => 50,
            'defect_qty' => 5,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task quantities updated successfully.',
                     'data' => [
                         'id' => $task->id,
                         'completed_qty' => 50,
                         'defect_qty' => 5,
                     ]
                 ]);

        $task->refresh();
        $this->assertEquals(50, $task->completed_qty);
        $this->assertEquals(5, $task->defect_qty);
    }

    public function test_cannot_update_completed_qty_beyond_target(): void
    {
        $task = Task::factory()->create([
            'target_qty' => 100
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/quantities", [
            'completed_qty' => 150, // More than target
            'defect_qty' => 5,
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Completed quantity cannot exceed target quantity.',
                 ]);
    }

    public function test_can_start_task_downtime(): void
    {
        $task = Task::factory()->create([
            'status' => 'IN_PROGRESS'
        ]);

        $response = $this->postJson("/api/tasks/{$task->id}/start-downtime");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Downtime started for the task.',
                     'data' => [
                         'id' => $task->id,
                         'status' => 'DOWNTIME',
                     ]
                 ]);

        $task->refresh();
        $this->assertEquals('DOWNTIME', $task->status);
        $this->assertNotNull($task->downtime_start);
    }

    public function test_can_end_task_downtime(): void
    {
        $task = Task::factory()->create([
            'status' => 'DOWNTIME',
            'downtime_start' => now()->subMinutes(30),
            'total_downtime_minutes' => 0,
        ]);

        $response = $this->postJson("/api/tasks/{$task->id}/end-downtime");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Downtime ended for the task.',
                     'data' => [
                         'id' => $task->id,
                         'status' => 'IN_PROGRESS',
                     ]
                 ]);

        $task->refresh();
        $this->assertEquals('IN_PROGRESS', $task->status);
        $this->assertNull($task->downtime_start);
        // The total downtime should be at least 29 minutes (allowing for small timing differences during test execution)
        // since 30 minutes have passed since downtime started
        $this->assertGreaterThanOrEqual(29, $task->total_downtime_minutes);
    }

    public function test_can_get_task_statistics(): void
    {
        // Create various tasks with different statuses
        Task::factory()->count(2)->create(['status' => 'PENDING']);
        Task::factory()->count(3)->create(['status' => 'IN_PROGRESS']);
        Task::factory()->count(1)->create(['status' => 'PAUSED']);
        Task::factory()->count(4)->create(['status' => 'COMPLETED']);
        Task::factory()->count(2)->create(['status' => 'DOWNTIME']);

        $response = $this->getJson('/api/tasks-statistics');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'total' => 12,
                         'pending' => 2,
                         'in_progress' => 3,
                         'paused' => 1,
                         'downtime' => 2,
                         'completed' => 4,
                     ]
                 ]);
    }

    public function test_can_filter_tasks_by_status(): void
    {
        Task::factory()->count(2)->create(['status' => 'PENDING']);
        Task::factory()->count(3)->create(['status' => 'IN_PROGRESS']);

        $response = $this->getJson('/api/tasks?status=IN_PROGRESS');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        // Check that all returned tasks have the correct status
        $responseData = $response->json('data.data');
        foreach ($responseData as $task) {
            $this->assertEquals('IN_PROGRESS', $task['status']);
        }

        $responseData = $response->json('data');
        $this->assertCount(3, $responseData['data']);
    }

    public function test_can_filter_tasks_by_project(): void
    {
        $project = Project::factory()->create();
        $otherProject = Project::factory()->create();

        Task::factory()->count(2)->create(['project_id' => $project->id]);
        Task::factory()->count(3)->create(['project_id' => $otherProject->id]);

        $response = $this->getJson("/api/tasks?project_id={$project->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        // Check that all returned tasks belong to the correct project
        $responseData = $response->json('data.data');
        foreach ($responseData as $task) {
            $this->assertEquals($project->id, $task['project_id']);
        }

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData['data']);
    }
}