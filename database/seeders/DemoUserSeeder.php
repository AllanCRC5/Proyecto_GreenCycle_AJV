<?php

namespace Database\Seeders;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario solo para demostración
        $user = User::create([
            'name' => 'Usuario Demo',
            'email' => 'demo@greencycle.test',
            'password' => 'password123', // el cast 'hashed' lo encripta solo
        ]);

        // Tres árboles, uno por estado
        Tree::factory()->for($user)->create(['level' => 3, 'progress' => 2]); // ACTIVE
        Tree::factory()->for($user)->mature()->create();                      // MATURE
        Tree::factory()->for($user)->dead()->create();                        // DEAD
    }
}
