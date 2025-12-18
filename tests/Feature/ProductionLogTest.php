<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ProductionLog;
use App\Models\Task;
use App\Models\Machine;
use App\Models\ProjectItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProductionLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Sanctum authentication by default for all tests except unauthorized test
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_get_production_logs_list(): void
    {
        // Arrange
        $this->actingAs($this->user, 'sanctum'); // Authenticate the user

        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $productionLogs = ProductionLog::factory()->count(3)->create([
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->getJson('/api/production-logs');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'task_id',
                                 'machine_id',
                                 'item_id',
                                 'project_id',
                                 'step',
                                 'shift',
                                 'good_qty',
                                 'defect_qty',
                                 'operator',
                                 'logged_at',
                                 'type',
                                 'created_at',
                                 'updated_at',
                                 'task',
                                 'machine',
                                 'item',
                                 'project'
                             ]
                         ]
                     ]
                 ])
                 ->assertJson(['success' => true]);
    }

    public function test_can_filter_production_logs_by_project(): void
    {
        // Arrange
        $this->actingAs($this->user, 'sanctum'); // Authenticate the user

        $project = Project::factory()->create();
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();

        $productionLog = ProductionLog::factory()->create([
            'project_id' => $project->id,
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
        ]);

        $otherProject = Project::factory()->create();
        $otherTask = Task::factory()->create();
        $otherMachine = Machine::factory()->create();
        $otherItem = ProjectItem::factory()->create();

        ProductionLog::factory()->create([
            'project_id' => $otherProject->id,
            'task_id' => $otherTask->id,
            'machine_id' => $otherMachine->id,
            'item_id' => $otherItem->id,
        ]);

        // Act
        $response = $this->getJson("/api/production-logs?project_id={$project->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(1, 'data.data');
    }

    public function test_can_filter_production_logs_by_machine(): void
    {
        // Arrange
        $this->actingAs($this->user, 'sanctum'); // Authenticate the user

        $machine = Machine::factory()->create();
        $task = Task::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $productionLog = ProductionLog::factory()->create([
            'machine_id' => $machine->id,
            'task_id' => $task->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        $otherMachine = Machine::factory()->create();
        $otherTask = Task::factory()->create();
        $otherItem = ProjectItem::factory()->create();
        $otherProject = Project::factory()->create();

        ProductionLog::factory()->create([
            'machine_id' => $otherMachine->id,
            'task_id' => $otherTask->id,
            'item_id' => $otherItem->id,
            'project_id' => $otherProject->id,
        ]);

        // Act
        $response = $this->getJson("/api/production-logs?machine_id={$machine->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(1, 'data.data');
    }

    public function test_can_store_new_production_log(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $data = [
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
            'step' => 'Assembly',
            'shift' => 'SHIFT_1',
            'good_qty' => 50,
            'defect_qty' => 2,
            'operator' => 'John Doe',
            'logged_at' => now(),
            'type' => 'OUTPUT'
        ];

        // Act
        $response = $this->postJson('/api/production-logs', $data);

        // Assert
        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Production log created successfully'
                 ]);

        $this->assertDatabaseHas('production_logs', [
            'task_id' => $data['task_id'],
            'machine_id' => $data['machine_id'],
            'item_id' => $data['item_id'],
            'project_id' => $data['project_id'],
            'step' => $data['step'],
            'shift' => $data['shift'],
            'good_qty' => $data['good_qty'],
            'defect_qty' => $data['defect_qty'],
            'operator' => $data['operator'],
            'type' => $data['type']
        ]);
    }

    public function test_cannot_store_invalid_production_log(): void
    {
        // Arrange
        $invalidData = [
            'task_id' => 'non-existent-id', // Invalid task_id
            'machine_id' => 'non-existent-id', // Invalid machine_id
            'item_id' => 'non-existent-id', // Invalid item_id
            'project_id' => 'non-existent-id', // Invalid project_id
            'step' => '', // Required field
            'shift' => 'INVALID_SHIFT', // Invalid shift
            'good_qty' => -10, // Negative value
            'defect_qty' => -5, // Negative value
            'operator' => '', // Required field
            'logged_at' => 'invalid-date', // Invalid date
            'type' => '' // Required field
        ];

        // Act
        $response = $this->postJson('/api/production-logs', $invalidData);

        // Assert
        $response->assertStatus(422)
                 ->assertJson(['success' => false]);
    }

    public function test_can_show_specific_production_log(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $productionLog = ProductionLog::factory()->create([
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->getJson("/api/production-logs/{$productionLog->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $productionLog->id,
                         'task_id' => $productionLog->task_id,
                         'machine_id' => $productionLog->machine_id,
                         'item_id' => $productionLog->item_id,
                         'project_id' => $productionLog->project_id,
                         'step' => $productionLog->step,
                         'shift' => $productionLog->shift,
                         'good_qty' => $productionLog->good_qty,
                         'defect_qty' => $productionLog->defect_qty,
                         'operator' => $productionLog->operator,
                         'type' => $productionLog->type
                     ]
                 ]);
    }

    public function test_returns_error_when_production_log_not_found(): void
    {
        // Act
        $response = $this->getJson('/api/production-logs/non-existent-id');

        // Assert
        $response->assertStatus(404)
                 ->assertJson(['success' => false]);
    }

    public function test_can_update_production_log(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $productionLog = ProductionLog::factory()->create([
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        $newData = [
            'step' => 'Updated Step',
            'shift' => 'SHIFT_2',
            'good_qty' => 100,
            'defect_qty' => 5,
            'operator' => 'Jane Doe',
            'type' => 'DOWNTIME_START'
        ];

        // Act
        $response = $this->putJson("/api/production-logs/{$productionLog->id}", $newData);

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Production log updated successfully'
                 ]);

        $this->assertDatabaseHas('production_logs', [
            'id' => $productionLog->id,
            'step' => $newData['step'],
            'shift' => $newData['shift'],
            'good_qty' => $newData['good_qty'],
            'defect_qty' => $newData['defect_qty'],
            'operator' => $newData['operator'],
            'type' => $newData['type']
        ]);
    }

    public function test_can_delete_production_log(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $productionLog = ProductionLog::factory()->create([
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->deleteJson("/api/production-logs/{$productionLog->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Production log deleted successfully'
                 ]);

        $this->assertDatabaseMissing('production_logs', [
            'id' => $productionLog->id
        ]);
    }

    public function test_can_get_production_logs_by_project(): void
    {
        // Arrange
        $project = Project::factory()->create();
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();

        $productionLogs = ProductionLog::factory()->count(2)->create([
            'project_id' => $project->id,
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
        ]);

        $otherProject = Project::factory()->create();
        $otherTask = Task::factory()->create();
        $otherMachine = Machine::factory()->create();
        $otherItem = ProjectItem::factory()->create();

        ProductionLog::factory()->create([
            'project_id' => $otherProject->id,
            'task_id' => $otherTask->id,
            'machine_id' => $otherMachine->id,
            'item_id' => $otherItem->id,
        ]);

        // Act
        $response = $this->getJson("/api/production-logs/project/{$project->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(2, 'data.data');
    }

    public function test_can_get_production_logs_by_machine(): void
    {
        // Arrange
        $machine = Machine::factory()->create();
        $task = Task::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $productionLogs = ProductionLog::factory()->count(2)->create([
            'machine_id' => $machine->id,
            'task_id' => $task->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        $otherMachine = Machine::factory()->create();
        $otherTask = Task::factory()->create();
        $otherItem = ProjectItem::factory()->create();
        $otherProject = Project::factory()->create();

        ProductionLog::factory()->create([
            'machine_id' => $otherMachine->id,
            'task_id' => $otherTask->id,
            'item_id' => $otherItem->id,
            'project_id' => $otherProject->id,
        ]);

        // Act
        $response = $this->getJson("/api/production-logs/machine/{$machine->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(2, 'data.data');
    }

    public function test_can_get_production_summary(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        ProductionLog::factory()->create([
            'good_qty' => 100,
            'defect_qty' => 10,
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        ProductionLog::factory()->create([
            'good_qty' => 200,
            'defect_qty' => 5,
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->getJson('/api/production-summary');

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'total_good_qty' => 300,
                         'total_defect_qty' => 15,
                         'total_logs' => 2,
                         'avg_good_qty' => 150,
                         'avg_defect_qty' => 7.5
                     ]
                 ]);
    }

    public function test_unauthorized_user_cannot_access_production_logs(): void
    {
        // Refresh the application instance to clear any authentication
        $this->refreshApplication();

        // Act - without authentication
        $response = $this->getJson('/api/production-logs');

        // Assert
        $response->assertStatus(401); // Unauthorized
    }

    public function test_can_filter_production_logs_by_shift(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        ProductionLog::factory()->create([
            'shift' => 'SHIFT_1',
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        $targetLog = ProductionLog::factory()->create([
            'shift' => 'SHIFT_2',
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->getJson('/api/production-logs?shift=SHIFT_2');

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(1, 'data.data');
    }

    public function test_can_filter_production_logs_by_date_range(): void
    {
        // Arrange
        $date1 = '2023-01-01';
        $date2 = '2023-12-31';

        $task = Task::factory()->create();
        $machine = Machine::factory()->create();
        $item = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        ProductionLog::factory()->create([
            'logged_at' => '2023-06-15',
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        ProductionLog::factory()->create([
            'logged_at' => '2022-01-01', // Outside range
            'task_id' => $task->id,
            'machine_id' => $machine->id,
            'item_id' => $item->id,
            'project_id' => $project->id,
        ]);

        // Act
        $response = $this->getJson("/api/production-logs?from_date={$date1}&to_date={$date2}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(1, 'data.data');
    }
}