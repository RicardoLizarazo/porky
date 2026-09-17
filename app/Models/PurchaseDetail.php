<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchase_id',
        'inventory_item_id',
        'unit_id',
        'quantity',
        'unit_cost',
        'base_quantity',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_cost' => 'float',
        'base_quantity' => 'float',
        'subtotal' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function returnDetails()
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    public function returnedQuantity(): float
    {
        return (float) $this->returnDetails()->sum('quantity');
    }

    public function remainingReturnableQuantity(): float
    {
        return max(0, (float) $this->base_quantity - $this->returnedQuantity());
    }
}
