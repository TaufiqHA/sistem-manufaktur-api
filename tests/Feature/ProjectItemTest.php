<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ProjectItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_can_list_project_items(): void
    {
        $project = Project::factory()->create();
        $projectItems = ProjectItem::factory()->count(3)->create(['project_id' => (string)$project->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson('/api/project-items');

        $response->assertOk()
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'project_id',
                             'name',
                             'dimensions',
                             'thickness',
                             'qty_set',
                             'qty_per_product',
                             'total_required_qty',
                             'quantity',
                             'unit',
                             'is_bom_locked',
                             'is_workflow_locked',
                             'workflow',
                             'created_at',
                             'updated_at',
                         ]
                     ]
                 ]);
    }

    public function test_can_show_single_project_item(): void
    {
        $project = Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => (string)$project->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson("/api/project-items/{$projectItem->id}");

        $response->assertOk()
                 ->assertJson([
                     'id' => $projectItem->id,
                     'project_id' => $projectItem->project_id,
                     'name' => $projectItem->name,
                     'dimensions' => $projectItem->dimensions,
                     'thickness' => $projectItem->thickness,
                     'qty_set' => $projectItem->qty_set,
                     'qty_per_product' => $projectItem->qty_per_product,
                     'total_required_qty' => $projectItem->total_required_qty,
                     'quantity' => $projectItem->quantity,
                     'unit' => $projectItem->unit,
                     'is_bom_locked' => $projectItem->is_bom_locked,
                     'is_workflow_locked' => $projectItem->is_workflow_locked,
                     'workflow' => $projectItem->workflow,
                 ]);
    }

    public function test_can_create_project_item(): void
    {
        $project = Project::factory()->create();

        $data = [
            'project_id' => (string)$project->id, // Convert to string as per migration
            'name' => 'Test Project Item',
            'dimensions' => '100x200x300',
            'thickness' => '12mm',
            'qty_set' => 1,
            'qty_per_product' => 2,
            'total_required_qty' => 20,
            'quantity' => 10,
            'unit' => 'pcs',
            'is_bom_locked' => false,
            'is_workflow_locked' => false,
            'workflow' => [],
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/project-items', $data);

        $response->assertCreated()
                 ->assertJson([
                     'project_id' => $data['project_id'],
                     'name' => $data['name'],
                     'dimensions' => $data['dimensions'],
                     'thickness' => $data['thickness'],
                     'qty_set' => $data['qty_set'],
                     'qty_per_product' => $data['qty_per_product'],
                     'total_required_qty' => $data['total_required_qty'],
                     'quantity' => $data['quantity'],
                     'unit' => $data['unit'],
                     'is_bom_locked' => $data['is_bom_locked'],
                     'is_workflow_locked' => $data['is_workflow_locked'],
                     'workflow' => $data['workflow'],
                 ]);

        $this->assertDatabaseHas('project_items', [
            'project_id' => $data['project_id'],
            'name' => $data['name'],
            'dimensions' => $data['dimensions'],
            'thickness' => $data['thickness'],
            'qty_set' => $data['qty_set'],
            'qty_per_product' => $data['qty_per_product'],
            'total_required_qty' => $data['total_required_qty'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'is_bom_locked' => $data['is_bom_locked'],
            'is_workflow_locked' => $data['is_workflow_locked'],
        ]);
    }

    public function test_can_update_project_item(): void
    {
        $project = Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => (string)$project->id]);

        $data = [
            'name' => 'Updated Project Item',
            'dimensions' => '400x500x600',
            'thickness' => '18mm',
            'qty_set' => 2,
            'qty_per_product' => 3,
            'total_required_qty' => 60,
            'quantity' => 20,
            'unit' => 'sets',
            'is_bom_locked' => true,
            'is_workflow_locked' => true,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                        ->putJson("/api/project-items/{$projectItem->id}", $data);

        $response->assertOk()
                 ->assertJson([
                     'id' => $projectItem->id,
                     'project_id' => $projectItem->project_id,
                     'name' => $data['name'],
                     'dimensions' => $data['dimensions'],
                     'thickness' => $data['thickness'],
                     'qty_set' => $data['qty_set'],
                     'qty_per_product' => $data['qty_per_product'],
                     'total_required_qty' => $data['total_required_qty'],
                     'quantity' => $data['quantity'],
                     'unit' => $data['unit'],
                     'is_bom_locked' => $data['is_bom_locked'],
                     'is_workflow_locked' => $data['is_workflow_locked'],
                 ]);

        $this->assertDatabaseHas('project_items', [
            'id' => $projectItem->id,
            'name' => $data['name'],
            'dimensions' => $data['dimensions'],
            'thickness' => $data['thickness'],
            'qty_set' => $data['qty_set'],
            'qty_per_product' => $data['qty_per_product'],
            'total_required_qty' => $data['total_required_qty'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'is_bom_locked' => $data['is_bom_locked'],
            'is_workflow_locked' => $data['is_workflow_locked'],
        ]);
    }

    public function test_can_delete_project_item(): void
    {
        $project = Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => (string)$project->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->deleteJson("/api/project-items/{$projectItem->id}");

        $response->assertOk()
                 ->assertJson([
                     'message' => 'Project Item deleted successfully'
                 ]);

        $this->assertDatabaseMissing('project_items', [
            'id' => $projectItem->id,
        ]);
    }

    public function test_requires_authentication_for_project_items_endpoints(): void
    {
        $project = Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);

        $this->getJson('/api/project-items')
             ->assertUnauthorized();

        $this->getJson("/api/project-items/{$projectItem->id}")
             ->assertUnauthorized();

        $this->postJson('/api/project-items', [])
             ->assertUnauthorized();

        $this->putJson("/api/project-items/{$projectItem->id}", [])
             ->assertUnauthorized();

        $this->deleteJson("/api/project-items/{$projectItem->id}")
             ->assertUnauthorized();
    }

    public function test_validates_required_fields_when_creating_project_item(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/project-items', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'project_id',
                     'name',
                     'dimensions',
                     'thickness',
                     'qty_set',
                     'qty_per_product',
                     'total_required_qty',
                     'quantity',
                     'unit',
                 ]);
    }

    public function test_validates_data_types_when_creating_project_item(): void
    {
        $project = Project::factory()->create();

        $invalidData = [
            'project_id' => $project->id,
            'name' => 12345,
            'dimensions' => 12345,
            'thickness' => 12345,
            'qty_set' => 'invalid',
            'qty_per_product' => 'invalid',
            'total_required_qty' => 'invalid',
            'quantity' => 'invalid',
            'unit' => 12345,
            'is_bom_locked' => 'invalid_boolean',
            'is_workflow_locked' => 'invalid_boolean',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/project-items', $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name',
                     'dimensions',
                     'thickness',
                     'qty_set',
                     'qty_per_product',
                     'total_required_qty',
                     'quantity',
                     'unit',
                     'is_bom_locked',
                     'is_workflow_locked',
                 ]);
    }
}