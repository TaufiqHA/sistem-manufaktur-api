<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\RfqItem;
use App\Models\Rfq;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class RfqItemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $user = \App\Models\User::factory()->create();
        Auth::login($user);
    }

    /** @test */
    public function it_can_list_rfq_items()
    {
        // Create some RFQ items
        $rfqItems = RfqItem::factory()->count(3)->create();

        $response = $this->getJson('/api/rfq-items');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_create_an_rfq_item()
    {
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'rfq_id' => $rfq->id,
            'material_id' => $material->id,
            'name' => $this->faker->word,
            'qty' => $this->faker->numberBetween(1, 100),
        ];

        $response = $this->postJson('/api/rfq-items', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'RFQ item created successfully',
                     'data' => [
                         'rfq_id' => $data['rfq_id'],
                         'material_id' => $data['material_id'],
                         'name' => $data['name'],
                         'qty' => $data['qty'],
                     ]
                 ]);

        $this->assertDatabaseHas('rfq_items', [
            'rfq_id' => $data['rfq_id'],
            'material_id' => $data['material_id'],
            'name' => $data['name'],
            'qty' => $data['qty'],
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_an_rfq_item()
    {
        $response = $this->postJson('/api/rfq-items', []);

        $response->assertStatus(422)
                 ->assertJson([
                     'error' => 'Validation failed'
                 ]);
    }

    /** @test */
    public function it_can_show_a_specific_rfq_item()
    {
        $rfqItem = RfqItem::factory()->create();

        $response = $this->getJson("/api/rfq-items/{$rfqItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $rfqItem->id,
                         'rfq_id' => $rfqItem->rfq_id,
                         'material_id' => $rfqItem->material_id,
                         'name' => $rfqItem->name,
                         'qty' => $rfqItem->qty,
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_an_rfq_item()
    {
        $rfqItem = RfqItem::factory()->create();
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'rfq_id' => $rfq->id,
            'material_id' => $material->id,
            'name' => $this->faker->word . ' updated',
            'qty' => $this->faker->numberBetween(101, 200),
        ];

        $response = $this->putJson("/api/rfq-items/{$rfqItem->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'RFQ item updated successfully',
                     'data' => [
                         'rfq_id' => $data['rfq_id'],
                         'material_id' => $data['material_id'],
                         'name' => $data['name'],
                         'qty' => $data['qty'],
                     ]
                 ]);

        $this->assertDatabaseHas('rfq_items', [
            'id' => $rfqItem->id,
            'rfq_id' => $data['rfq_id'],
            'material_id' => $data['material_id'],
            'name' => $data['name'],
            'qty' => $data['qty'],
        ]);
    }

    /** @test */
    public function it_can_delete_an_rfq_item()
    {
        $rfqItem = RfqItem::factory()->create();

        $response = $this->deleteJson("/api/rfq-items/{$rfqItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('rfq_items', [
            'id' => $rfqItem->id,
        ]);
    }

    /** @test */
    public function it_can_get_rfq_items_by_rfq()
    {
        $rfq = Rfq::factory()->create();
        $rfqItem1 = RfqItem::factory()->create(['rfq_id' => $rfq->id]);
        $rfqItem2 = RfqItem::factory()->create(['rfq_id' => $rfq->id]);
        // Create another RFQ item with different RFQ
        $otherRfqItem = RfqItem::factory()->create();

        $response = $this->getJson("/api/rfq-items-by-rfq/{$rfq->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         [
                             'rfq_id' => $rfq->id,
                         ],
                         [
                             'rfq_id' => $rfq->id,
                         ]
                     ]
                 ])
                 ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_returns_404_when_getting_rfq_items_by_nonexistent_rfq()
    {
        $response = $this->getJson('/api/rfq-items-by-rfq/999999');

        $response->assertStatus(404);
    }
}