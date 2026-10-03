<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    //
    protected $fillable=[
        'quantity'
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
