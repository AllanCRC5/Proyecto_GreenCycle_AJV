<?php

namespace Database\Seeders;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@greencycle.test'],
            [
                'name' => 'Usuario Demo',
                'password' => 'password123',
            ]
        );

        // Limpiar únicamente los árboles del usuario de demostración.
        Tree::where('user_id', $user->id)->delete();

        // Árbol ACTIVE normal.
        Tree::factory()->for($user)->create([
            'level' => 3,
            'progress' => 2,
        ]);

        // Árbol ACTIVE con deterioro pendiente.
        Tree::factory()->for($user)->readyForDecay()->create();

        // Árbol MATURE.
        Tree::factory()->for($user)->mature()->create();

        // Árbol DEAD.
        Tree::factory()->for($user)->dead()->create();
    }
}
