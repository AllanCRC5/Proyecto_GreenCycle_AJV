<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    //
    protected $fillable=[
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function item():BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
