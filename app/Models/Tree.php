<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    //
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

    public function effect(): HasMany
    {
        return $this->hasMany(Effect::class);
    }
}
