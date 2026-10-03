<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Item extends Model
{
    //
    protected $fillable=[
        'name',
        'effect_duration',
        'effect_type',
        'description',
        'price',
    ];

    public function inventory():HasMany
    {
        return $this->hasMany(Inventory::class);
    }
    public function effect():HasMany
    {
        return $this->hasMany(Effect::class);
    }
    public function purchase():HasMany
    {
        return $this->hasMany(Purchase::class);
    }

}
