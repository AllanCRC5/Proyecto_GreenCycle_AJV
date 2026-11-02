<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // limpia y prepara la base de datos para cada prueba
    use RefreshDatabase;

    // registrar un usuario
    public function test_user_can_register(): void
    {
        // simula una petición POST enviando datos JSON al registro.
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Jean Marco',
            'email' => 'jean@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertCreated(); // comprueba que la respuesta sea HTTP 201.
        // Comprueba que existan los campos esperados.
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'coins',
            ],
            'token',
        ]);
        // revisa que el usuario se haya guardado en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
        ]);
    }

    // datos inválidos
    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => '',
            'email' => 'correo-invalido',
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
    }

    // iniciar sesión correctamente
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([// crea usuario en la base de datos.
            'email' => 'jean@example.com',
            'password' => 'Password123!',
        ]);

        $response = $this->postJson('/api/v1/login', [// enviamos correo y contraseña
            'email' => 'jean@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertOk(); // recibimos 200
        // verifica que la respuesta contenga los datos del usuario y el token.
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'coins',
            ],
            'token',
        ]);
    }

    // datos inválidos al login
    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'jean@example.com',
            'password' => 'Password123!',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'jean@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'email',
        ]);
    }

    // consultar el usuario autenticado
    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me');

        $response->assertOk();

        $response->assertJsonPath('data.id', $user->id);
        $response->assertJsonPath('data.email', $user->email);
    }

    // alguien sin iniciar seciono no puede consultar ese perfil
    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertUnauthorized()
            ->assertJsonPath('code', 'UNAUTHENTICATED')
            ->assertJsonStructure([
                'code',
                'message',
            ]);
    }

    // cerrar sesión mediante POST
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/logout');

        $response->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
