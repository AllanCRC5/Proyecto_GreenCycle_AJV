<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_tree_with_initial_values(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/tree', [
            'seed_type' => 'BASIC',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.seed_type', 'BASIC')
            ->assertJsonPath('data.level', 0)
            ->assertJsonPath('data.health', 100)
            ->assertJsonPath('data.progress', 0)
            ->assertJsonPath('data.status', 'ACTIVE');

        $this->assertDatabaseHas('trees', [
            'user_id' => $user->id,
            'seed_type' => 'BASIC',
            'level' => 0,
            'health' => 100,
            'progress' => 0,
            'status' => 'ACTIVE',
        ]);

        $this->assertNotNull(
            $response->json('data.last_decay_at')
        );
    }
}
