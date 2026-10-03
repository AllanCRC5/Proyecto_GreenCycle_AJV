<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    //
    protected $fillable=[
        'level',
        'health',
        'progress',
        'status',
        'last_care_at',
        'next_care_at',
        'last_decay_at',
        'harvested_at',
    ];

    public function user():belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function effect():HasMany
    {
        return $this->hasMany(Effect::class);
    }
}
