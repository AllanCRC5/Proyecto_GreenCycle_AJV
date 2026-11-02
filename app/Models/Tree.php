<?php

namespace App\Models;

use App\Enums\SeedType;
use App\Enums\TreeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seed_type',
    ];

    protected function casts(): array
    {
        return [
            'seed_type' => SeedType::class,
            'status' => TreeStatus::class,
            'last_care_at' => 'datetime',
            'next_care_at' => 'datetime',
            'last_decay_at' => 'datetime',
            'harvested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }

    public function careRequired(): int
    {
        return $this->seed_type->careRequiredPerLevel();
    }

    public function isInCooldown(): bool
    {
        return $this->next_care_at !== null
            && $this->next_care_at->isFuture();
    }

    public function applyCare(): void
    {
        $this->progress++;

        $this->health = min(
            config('greencycle.health_max', 100),
            $this->health + config('greencycle.care_heal', 20)
        );

        if ($this->progress >= $this->careRequired()) {
            $this->level++;
            $this->progress = 0;
        }

        if ($this->level >= config('greencycle.max_level', 10)) {
            $this->status = TreeStatus::MATURE;
        }

        $now = now();

        $this->last_care_at = $now;
        $this->next_care_at = $now->copy()->addMinutes(
            config('greencycle.care_cooldown_minutes', 60)
        );

        $this->save();
    }

    public function applyDecay(): void
    {
        if ($this->status !== TreeStatus::ACTIVE) {
            return;
        }

        $interval = (int) config(
            'greencycle.decay_interval_minutes',
            1440
        );

        $amount = (int) config('greencycle.decay_amount', 20);

        if ($interval <= 0 || $this->created_at === null) {
            return;
        }

        $reference = $this->created_at;

        if (
            $this->last_care_at !== null
            && $this->last_care_at->gt($reference)
        ) {
            $reference = $this->last_care_at;
        }

        if (
            $this->last_decay_at !== null
            && $this->last_decay_at->gt($reference)
        ) {
            $reference = $this->last_decay_at;
        }

        $now = now();
        $elapsedSeconds = $now->getTimestamp() - $reference->getTimestamp();
        $intervalSeconds = $interval * 60;

        if ($elapsedSeconds < $intervalSeconds) {
            return;
        }

        $intervals = intdiv($elapsedSeconds, $intervalSeconds);

        $this->health = max(
            0,
            $this->health - ($amount * $intervals)
        );

        $this->last_decay_at = $reference->copy()->addSeconds(
            $intervals * $intervalSeconds
        );

        if ($this->health <= 0) {
            $this->status = TreeStatus::DEAD;
        }

        $this->save();
    }
}
