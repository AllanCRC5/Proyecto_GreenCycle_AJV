<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreeFactory extends Factory
{
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

    public function mature(): static
    {
        return $this->state(fn () => [
            'level' => 10,
            'status' => 'MATURE',
        ]);
    }

    public function dead(): static
    {
        return $this->state(fn () => [
            'health' => 0,
            'status' => 'DEAD',
        ]);
    }

    public function readyForDecay(): static
    {
        $date = now()->subMinutes(
            config('greencycle.decay_interval_minutes', 1440) * 2
        );

        return $this->state(fn () => [
            'status' => 'ACTIVE',
            'health' => 100,
            'created_at' => $date,
            'last_care_at' => null,
            'last_decay_at' => $date,
        ]);
    }
}
