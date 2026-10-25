<?php

namespace Tests\Feature;

use App\Models\Tree;
use App\Models\User;
use Carbon\Carbon;
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

        $response = $this->postJson('/api/v1/trees', [
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

    public function test_user_cannot_exceed_active_tree_limit(): void
    {
        $user = User::factory()->create(); // crea un usuario de prueba

        Sanctum::actingAs($user); // simula que el usuario inició sesión

        Tree::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
        ]); // crea cinco arboles activos para ese usuario

        $response = $this->postJson('/api/v1/trees', [
            'seed_type' => 'BASIC',
        ]); // intenta crear un sexto arbol

        $response->assertStatus(409)
            ->assertJsonPath('code', 'TREE_LIMIT_REACHED'); // comprueba que la API responda con 409
        // 409: El código 409 indica que la solicitud entra en conflicto con una regla del sistema.
        $this->assertDatabaseCount('trees', 5); // que solo siga existiendo 5 arboles
    }

    public function test_user_can_care_for_an_active_tree(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'seed_type' => 'BASIC',
            'status' => 'ACTIVE',
            'level' => 0,
            'health' => 60,
            'progress' => 0,
            'last_care_at' => null,
            'next_care_at' => null,
            'last_decay_at' => now(),
        ]);

        $response = $this->postJson(
            "/api/v1/trees/{$tree->id}/care"
        );

        $response->assertOk()
            ->assertJsonPath('data.health', 80)
            ->assertJsonPath('data.progress', 1)
            ->assertJsonPath('data.status', 'ACTIVE');

        $this->assertDatabaseHas('trees', [
            'id' => $tree->id,
            'health' => 80,
            'progress' => 1,
            'status' => 'ACTIVE',
        ]);

        $tree->refresh();

        $this->assertNotNull($tree->last_care_at);
        $this->assertNotNull($tree->next_care_at);

        $this->assertEquals(
            60,
            $tree->last_care_at->diffInMinutes($tree->next_care_at)
        );
    }

    // un usuario puede cuidar un árbol activo, que la vida aumenta de 60 a 80, que el progreso pasa de 0 a 1 y que se registran las fechas del cuidado y del próximo cuidado.
    // Recupera vida, aumenta el progreso y registra las fechas.
    // Impedir cuidados durante el cooldown
    // Devuelve 409 y CARE_COOLDOWN sin modificar la vida ni el progreso.

    public function test_user_cannot_care_for_tree_during_cooldown(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-25 10:00:00'));

        try {
            $user = User::factory()->create();

            Sanctum::actingAs($user);

            $tree = Tree::factory()->create([
                'user_id' => $user->id,
                'seed_type' => 'BASIC',
                'status' => 'ACTIVE',
                'level' => 0,
                'health' => 60,
                'progress' => 0,
                'last_care_at' => now()->subMinutes(10),
                'next_care_at' => now()->addMinutes(50),
                'last_decay_at' => now(),
            ]);

            $response = $this->postJson(
                "/api/v1/trees/{$tree->id}/care"
            );

            $response->assertStatus(409)
                ->assertJsonPath('code', 'CARE_COOLDOWN');

            $this->assertDatabaseHas('trees', [
                'id' => $tree->id,
                'health' => 60,
                'progress' => 0,
            ]);
        } finally {
            Carbon::setTestNow();
        }
    }

    // Si el arbol muere, ya no se puede cuidar
    // Devuelve 409 y TREE_DEAD, sin revivir el árbo

    public function test_user_cannot_care_for_a_dead_tree(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'seed_type' => 'BASIC',
            'status' => 'DEAD',
            'level' => 0,
            'health' => 0,
            'progress' => 0,
            'last_care_at' => null,
            'next_care_at' => null,
            'last_decay_at' => now(),
        ]);

        $response = $this->postJson(
            "/api/v1/trees/{$tree->id}/care"
        );

        $response->assertStatus(409)
            ->assertJsonPath('code', 'TREE_DEAD');

        $this->assertDatabaseHas('trees', [
            'id' => $tree->id,
            'health' => 0,
            'progress' => 0,
            'status' => 'DEAD',
        ]);
    }

    // El usuario no puede cuidar el arbol de otra persona. Api devuelve 404 Not Found
    public function test_user_cannot_care_for_another_users_tree(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $tree = Tree::factory()->create([
            'user_id' => $anotherUser->id,
            'seed_type' => 'BASIC',
            'status' => 'ACTIVE',
            'level' => 0,
            'health' => 60,
            'progress' => 0,
            'last_care_at' => null,
            'next_care_at' => null,
            'last_decay_at' => now(),
        ]);

        $response = $this->postJson(
            "/api/v1/trees/{$tree->id}/care"
        );

        $response->assertNotFound()
            ->assertJsonPath('code', 'TREE_NOT_FOUND');

        $this->assertDatabaseHas('trees', [
            'id' => $tree->id,
            'user_id' => $anotherUser->id,
            'health' => 60,
            'progress' => 0,
            'status' => 'ACTIVE',
        ]);
    }

    // La API está rechazando el cuidado de árboles que ya alcanzaron el estado MATURE.
    public function test_user_cannot_care_for_a_mature_tree(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'seed_type' => 'BASIC',
            'status' => 'MATURE',
            'level' => 10,
            'health' => 100,
            'progress' => 0,
            'last_care_at' => null,
            'next_care_at' => null,
            'last_decay_at' => now(),
        ]);

        $response = $this->postJson(
            "/api/v1/trees/{$tree->id}/care"
        );

        $response->assertStatus(409)
            ->assertJsonPath('code', 'TREE_NOT_ACTIVE');

        $this->assertDatabaseHas('trees', [
            'id' => $tree->id,
            'health' => 100,
            'progress' => 0,
            'status' => 'MATURE',
        ]);
    }

    // Cuidar un árbol activo.
    // Bloquear cuidados durante el cooldown.
    // Bloquear cuidados de árboles muertos.
    // Impedir cuidar árboles de otros usuarios.
    // Impedir cuidar árboles maduros.

    // después de 1440 minutos (24 horas), un árbol con 100 de vida pierde 20 puntos y queda con 80.

    public function test_tree_loses_health_after_decay_interval(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-25 10:00:00'));

        try {
            $user = User::factory()->create();

            $tree = Tree::factory()->create([
                'user_id' => $user->id,
                'status' => 'ACTIVE',
                'health' => 100,
                'created_at' => now(),
                'last_care_at' => null,
                'last_decay_at' => null,
            ]);

            // Avanzamos el reloj 24 horas.
            Carbon::setTestNow(
                Carbon::parse('2026-10-26 10:00:00')
            );

            $tree->applyDecay();
            $tree->refresh();

            $this->assertEquals(80, $tree->health);
        } finally {
            Carbon::setTestNow();
        }
    }

    // Después de 24 horas: la salud baja de 100 a 80.
    // Antes de las 24 horas: la salud se mantiene en 100.

    public function test_tree_does_not_lose_health_before_decay_interval(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-25 10:00:00'));

        try {
            $user = User::factory()->create();

            $tree = Tree::factory()->create([
                'user_id' => $user->id,
                'status' => 'ACTIVE',
                'health' => 100,
                'created_at' => now(),
                'last_care_at' => null,
                'last_decay_at' => null,
            ]);

            // Solo han pasado 12 horas.
            Carbon::setTestNow(
                Carbon::parse('2026-10-25 22:00:00')
            );

            $tree->applyDecay();
            $tree->refresh();

            $this->assertEquals(100, $tree->health);
        } finally {
            Carbon::setTestNow();
        }
    }

    // Que el árbol pierde los 20 puntos de salud que le quedan y, al llegar a cero, su estado cambia automáticamente a DEAD.

    public function test_tree_dies_when_health_reaches_zero(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-25 10:00:00'));

        try {
            $user = User::factory()->create();

            $tree = Tree::factory()->create([
                'user_id' => $user->id,
                'status' => 'ACTIVE',
                'health' => 20,
                'created_at' => now(),
                'last_care_at' => null,
                'last_decay_at' => null,
            ]);

            // Avanzamos 24 horas.
            Carbon::setTestNow(
                Carbon::parse('2026-10-26 10:00:00')
            );

            $tree->applyDecay();
            $tree->refresh();

            $this->assertEquals(0, $tree->health);
            $this->assertEquals('DEAD', $tree->status->value);
        } finally {
            Carbon::setTestNow();
        }
    }

    // El árbol pierde salud después de 24 horas.
    // El árbol no pierde salud antes de las 24 horas.
    // Si la salud llega a cero, el árbol pasa a DEAD.

    // Ya comprobamos que un árbol muerto no sigue perdiendo salud ni cambia su estado.
    public function test_dead_tree_does_not_lose_more_health(): void
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'status' => 'DEAD',
            'health' => 0,
            'created_at' => now()->subMinutes(2880),
            'last_care_at' => null,
            'last_decay_at' => null,
        ]);

        $tree->applyDecay();

        $tree->refresh();

        $this->assertEquals(0, $tree->health);
        $this->assertEquals('DEAD', $tree->status->value);
    }

    // Si se ejecuta el comando dos veces seguidas, el árbol no pierda otros 20 puntos de salud por el mismo periodo de 24 horas

    public function test_tree_does_not_apply_decay_twice_for_same_period(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-25 10:00:00'));

        try {
            $user = User::factory()->create();

            $tree = Tree::factory()->create([
                'user_id' => $user->id,
                'status' => 'ACTIVE',
                'health' => 100,
                'created_at' => now(),
                'last_care_at' => null,
                'last_decay_at' => null,
            ]);

            // Después de 24 horas, pierde 20 puntos.
            Carbon::setTestNow(
                Carbon::parse('2026-10-26 10:00:00')
            );

            $tree->applyDecay();
            $tree->refresh();

            $this->assertEquals(80, $tree->health);

            // Ejecutamos otra vez sin avanzar el reloj.
            $tree->applyDecay();
            $tree->refresh();

            $this->assertEquals(80, $tree->health);
        } finally {
            Carbon::setTestNow();
        }
    }

    // El arbol comienza en 0, con progreso 4,
    public function test_caring_for_tree_increases_level_and_resets_progress(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'seed_type' => 'BASIC',
            'status' => 'ACTIVE',
            'level' => 0,
            'health' => 100,
            'progress' => 4,
            'last_care_at' => null,
            'next_care_at' => null,
            'last_decay_at' => now(),
        ]);

        $response = $this->postJson(
            "/api/v1/trees/{$tree->id}/care"
        );

        $response->assertOk()
            ->assertJsonPath('data.level', 1)
            ->assertJsonPath('data.progress', 0)
            ->assertJsonPath('data.status', 'ACTIVE');

        $this->assertDatabaseHas('trees', [
            'id' => $tree->id,
            'level' => 1,
            'progress' => 0,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_tree_becomes_mature_when_reaching_max_level(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'seed_type' => 'BASIC',
            'status' => 'ACTIVE',
            'level' => 9,
            'health' => 100,
            'progress' => 4,
            'last_care_at' => null,
            'next_care_at' => null,
            'last_decay_at' => now(),
        ]);

        $response = $this->postJson(
            "/api/v1/trees/{$tree->id}/care"
        );

        $response->assertOk()
            ->assertJsonPath('data.level', 10)
            ->assertJsonPath('data.progress', 0)
            ->assertJsonPath('data.status', 'MATURE');

        $this->assertDatabaseHas('trees', [
            'id' => $tree->id,
            'level' => 10,
            'progress' => 0,
            'status' => 'MATURE',
        ]);
    }
    // El árbol sube de nivel cuando completa el progreso requerido.
    // El progreso vuelve a 0 después de subir de nivel.
    // Al llegar al nivel 10, el árbol cambia a MATURE.
    // Las pruebas anteriores siguen funcionando.

    public function test_decay_command_reduces_health_of_active_tree(): void
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
            'health' => 100,
            'created_at' => now()->subMinutes(1440),
            'last_care_at' => null,
            'last_decay_at' => null,
        ]);

        $this->artisan('trees:apply-decay')
            ->assertSuccessful();

        $tree->refresh();

        $this->assertEquals(80, $tree->health);
        $this->assertEquals('ACTIVE', $tree->status->value);
    }

    public function test_user_cannot_create_special_tree_in(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/trees', [
            'seed_type' => 'SPECIAL',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount('trees', 0);
    }

    public function test_user_cannot_view_another_users_tree(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/trees/{$tree->id}")
            ->assertNotFound();
    }
}
