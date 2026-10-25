<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreeFactory extends Factory
{
    // Un árbol "normal": recién plantado
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'seed_type' => 'BASIC',
            'level' => 0,
            'health' => 100,
            'progress' => 0,
            'status' => 'ACTIVE',
            'last_decay_at' => now(),
        ];
    }

    // Variante: árbol listo para cosechar
    public function mature(): static
    {
        return $this->state(fn () => [
            'level' => 10,
            'status' => 'MATURE',
        ]);
    }

    // Variante: árbol muerto
    public function dead(): static
    {
        return $this->state(fn () => [
            'health' => 0,
            'status' => 'DEAD',
        ]);
    }
}
