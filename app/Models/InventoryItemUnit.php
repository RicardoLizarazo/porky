<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItemUnit extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'unit_id',
        'factor_to_base',
    ];

    protected $casts = [
        'factor_to_base' => 'float',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
