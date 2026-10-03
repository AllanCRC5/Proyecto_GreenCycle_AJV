<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Effect extends Model
{
    //
    protected $fillable = [
        'effect_type',
        'expires_at',
        'started_at',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }
}
