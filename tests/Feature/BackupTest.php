<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Backup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_all_backups()
    {
        $user = User::factory()->create();
        
        // Create sample backups
        Backup::factory()->count(3)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/backups');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'filename',
                        'path',
                        'disk',
                        'size',
                        'status',
                        'type',
                        'details',
                        'completed_at',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_new_backup()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/backups', [
                'type' => 'full'
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Backup creation initiated'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'filename',
                    'path',
                    'disk',
                    'status',
                    'type',
                    'created_by'
                ]
            ]);

        $this->assertDatabaseHas('backups', [
            'type' => 'full',
            'status' => 'pending',
            'created_by' => $this->user->name
        ]);
    }

    /** @test */
    public function it_validates_backup_creation_request()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/backups', [
                'type' => 'invalid_type'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed'
            ]);
    }

    /** @test */
    public function it_can_show_a_specific_backup()
    {
        $backup = Backup::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/backups/{$backup->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $backup->id,
                    'filename' => $backup->filename
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_when_backup_not_found()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/backups/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Backup not found'
            ]);
    }

    /** @test */
    public function it_can_update_a_backup()
    {
        $backup = Backup::factory()->create([
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/backups/{$backup->id}", [
                'status' => 'completed',
                'size' => 1024000,
                'completed_at' => now()
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Backup updated successfully'
            ]);

        $this->assertDatabaseHas('backups', [
            'id' => $backup->id,
            'status' => 'completed',
            'size' => 1024000
        ]);
    }

    /** @test */
    public function it_validates_backup_update_request()
    {
        $backup = Backup::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/backups/{$backup->id}", [
                'status' => 'invalid_status',
                'size' => 'not_an_integer'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed'
            ]);
    }

    /** @test */
    public function it_can_delete_a_backup()
    {
        // Create a fake file in storage to test file deletion
        Storage::fake('local');
        $file = UploadedFile::fake()->create('test_backup.sql', 100);
        $path = 'backups/test/';
        Storage::put($path . $file->getClientOriginalName(), $file->getContent());

        $backup = Backup::factory()->create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'local'
        ]);

        // Verify the file exists before deletion
        Storage::disk('local')->assertExists($path . $file->getClientOriginalName());

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/backups/{$backup->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Backup deleted successfully'
            ]);

        $this->assertDatabaseMissing('backups', [
            'id' => $backup->id
        ]);

        // Verify the file was deleted
        Storage::disk('local')->assertMissing($path . $file->getClientOriginalName());
    }

    /** @test */
    public function it_can_get_backup_stats()
    {
        // Create sample backups with different statuses
        Backup::factory()->count(3)->create();
        Backup::factory()->create(['status' => 'completed', 'size' => 1024000]);
        Backup::factory()->create(['status' => 'failed', 'size' => 512000]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/backups/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_backups',
                    'total_size_bytes',
                    'total_size_formatted',
                    'latest_backup' => [
                        'id',
                        'filename',
                        'path',
                        'disk',
                        'size',
                        'status',
                        'type',
                        'details',
                        'completed_at',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ],
                    'status_counts'
                ]
            ]);

        // Check that the data structure is correct without specific values
        $responseData = $response->json('data');
        $this->assertIsInt($responseData['total_backups']);
        $this->assertGreaterThanOrEqual(5, $responseData['total_backups']); // At least 5 we created
        $this->assertIsInt($responseData['total_size_bytes']);
        $this->assertIsString($responseData['total_size_formatted']);
        $this->assertIsArray($responseData['status_counts']);
    }

    /** @test */
    public function it_requires_authentication_for_backup_routes()
    {
        // Test that unauthenticated requests return 401
        $response = $this->getJson('/api/backups');
        $response->assertUnauthorized();

        $response = $this->postJson('/api/backups', []);
        $response->assertUnauthorized();

        $backup = Backup::factory()->create();
        $response = $this->getJson("/api/backups/{$backup->id}");
        $response->assertUnauthorized();
    }
}