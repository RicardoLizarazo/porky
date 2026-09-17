<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'base_unit_id');
    }
}
