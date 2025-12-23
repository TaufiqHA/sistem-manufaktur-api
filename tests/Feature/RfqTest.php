<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rfq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RfqTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
    }

    /** @test */
    public function it_can_create_an_rfq(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'code' => 'RFQ-TEST-001',
            'date' => '2023-12-23 10:00:00',
            'description' => 'Test RFQ description',
            'status' => 'DRAFT',
        ];

        $response = $this->postJson('/api/rfqs', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'RFQ created successfully',
                     'data' => [
                         'code' => 'RFQ-TEST-001',
                         'description' => 'Test RFQ description',
                         'status' => 'DRAFT',
                     ]
                 ]);

        $this->assertDatabaseHas('rfqs', [
            'code' => 'RFQ-TEST-001',
            'description' => 'Test RFQ description',
            'status' => 'DRAFT',
        ]);
    }

    /** @test */
    public function it_can_list_all_rfqs(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Rfq::factory()->count(3)->create();

        $response = $this->getJson('/api/rfqs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data',
                     'pagination'
                 ])
                 ->assertJson([
                     'message' => 'RFQs retrieved successfully',
                 ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /** @test */
    public function it_can_show_a_single_rfq(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $rfq = Rfq::factory()->create();

        $response = $this->getJson("/api/rfqs/{$rfq->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $rfq->id,
                         'code' => $rfq->code,
                         'description' => $rfq->description,
                         'status' => $rfq->status,
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_an_rfq(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $rfq = Rfq::factory()->create([
            'code' => 'RFQ-OLD-001',
            'status' => 'DRAFT',
        ]);

        $updatedData = [
            'code' => 'RFQ-NEW-001',
            'status' => 'PO_CREATED',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/rfqs/{$rfq->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'RFQ updated successfully',
                     'data' => [
                         'code' => 'RFQ-NEW-001',
                         'status' => 'PO_CREATED',
                         'description' => 'Updated description',
                     ]
                 ]);

        $this->assertDatabaseHas('rfqs', [
            'id' => $rfq->id,
            'code' => 'RFQ-NEW-001',
            'status' => 'PO_CREATED',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function it_can_delete_an_rfq(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $rfq = Rfq::factory()->create();

        $response = $this->deleteJson("/api/rfqs/{$rfq->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'RFQ deleted successfully'
                 ]);

        $this->assertDatabaseMissing('rfqs', [
            'id' => $rfq->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_an_rfq(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'description' => 'Test description without required fields',
        ];

        $response = $this->postJson('/api/rfqs', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'The code field is required. (and 2 more errors)',
                 ])
                 ->assertJsonStructure([
                     'errors' => [
                         'code',
                         'date',
                         'status'
                     ]
                 ]);
    }

    /** @test */
    public function it_filters_rfqs_by_status(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Rfq::factory()->create(['status' => 'DRAFT']);
        Rfq::factory()->create(['status' => 'PO_CREATED']);

        $response = $this->getJson('/api/rfqs?status=DRAFT');

        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $draftRfqs = array_filter($responseData, function ($rfq) {
            return $rfq['status'] === 'DRAFT';
        });

        $this->assertCount(count($responseData), $draftRfqs);
    }

    /** @test */
    public function it_searches_rfqs_by_code_and_description(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Rfq::factory()->create([
            'code' => 'RFQ-SEARCH-TEST',
            'description' => 'This is a test description',
        ]);
        Rfq::factory()->create([
            'code' => 'RFQ-OTHER-001',
            'description' => 'Another description',
        ]);

        $response = $this->getJson('/api/rfqs?search=SEARCH');

        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $searchResults = array_filter($responseData, function ($rfq) {
            return str_contains($rfq['code'], 'SEARCH');
        });

        $this->assertCount(count($responseData), $searchResults);
    }
}