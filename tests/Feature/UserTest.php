<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_fetched_by_admin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['view_users'],
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_users_cannot_be_fetched_without_permission()
    {
        $user = User::factory()->create([
            'role' => 'operator',
            'permissions' => [],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_user_can_be_created_by_admin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['create_users'],
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'operator',
                'permissions' => ['view_products'],
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'operator',
        ]);
    }

    public function test_user_creation_validation()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['create_users'],
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => '', // Invalid (empty)
                'email' => 'invalid-email', // Invalid email
                'password' => '123', // Too short
                'password_confirmation' => 'different', // Does not match
                'role' => 'invalid_role', // Invalid role
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function test_user_can_be_shown_by_admin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['view_users'],
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_user_showing_requires_permission()
    {
        $user = User::factory()->create([
            'role' => 'operator',
            'permissions' => [],
        ]);

        $targetUser = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/users/{$targetUser->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_be_updated_by_admin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['edit_users'],
        ]);

        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$user->id}", [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'manager',
                'permissions' => ['view_reports'],
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'manager',
        ]);
    }

    public function test_user_update_validation()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['edit_users'],
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$user->id}", [
                'email' => 'invalid-email', // Invalid email
                'role' => 'invalid_role', // Invalid role
                'password' => '123', // Too short
                'password_confirmation' => 'different', // Does not match
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'role', 'password']);
    }

    public function test_user_update_requires_permission()
    {
        $user = User::factory()->create([
            'role' => 'operator',
            'permissions' => [],
        ]);

        $targetUser = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/users/{$targetUser->id}", [
                'name' => 'New Name',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_be_deleted_by_admin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['delete_users'],
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_deletion_requires_permission()
    {
        $user = User::factory()->create([
            'role' => 'operator',
            'permissions' => [],
        ]);

        $targetUser = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_themselves()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['delete_users'],
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Cannot delete your own account.',
        ]);
    }

    public function test_users_api_with_search_functionality()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['view_users'],
        ]);

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=John');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'John Doe',
        ]);
    }

    public function test_non_authenticated_requests_are_rejected()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);

        $user = User::factory()->create();

        $response = $this->postJson('/api/users', []);
        $response->assertStatus(401);

        $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(401);

        $response = $this->putJson("/api/users/{$user->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/users/{$user->id}");
        $response->assertStatus(401);
    }
}