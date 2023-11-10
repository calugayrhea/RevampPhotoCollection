<?php

namespace Tests\Feature;

use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created()
    {
        $data = [
            'name' => 'Test Collection',
            'owner_email' => 'test@example.com',
        ];

        $response = $this->post('/api/apiCollections', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Successfully created a collection']);
    }

    public function test_it_can_be_updated_by_owner()
    {
        $collection = Collection::factory()->create([
            'owner_email' => 'test@example.com',
        ]);

        $newData = [
            'name' => 'Updated Collection',
            'owner_email' => 'test@example.com',
        ];

        $response = $this->put(route('apiCollections.update', ['apiCollection' => $collection->id]), $newData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully updated the collection']);
    }

    public function test_it_cannot_be_updated_by_non_owner()
    {
        $collection = Collection::factory()->create();
        $newData = [
            'name' => 'Updated Collection',
            'owner_email' => 'non-owner@example.com',
        ];

        $response = $this->put("/api/apiCollections/{$collection->id}", $newData);

        $response->assertStatus(403);
    }

    public function test_it_can_be_deleted()
    {
        $collection = Collection::factory()->create();

        $response = $this->delete("/api/apiCollections/{$collection->id}");

        $response->assertStatus(204);
    }
}
