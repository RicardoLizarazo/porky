<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    const TYPE_PURCHASE_IN = 'purchase_in';
    const TYPE_PURCHASE_RETURN_OUT = 'purchase_return_out';
    const TYPE_WASTE_OUT = 'waste_out';
    const TYPE_EMPLOYEE_CONSUMPTION_OUT = 'employee_consumption_out';
    const TYPE_ADJUSTMENT = 'adjustment';

    public static array $types = [
        self::TYPE_PURCHASE_IN => 'Entrada por compra',
        self::TYPE_PURCHASE_RETURN_OUT => 'Devolución a proveedor',
        self::TYPE_WASTE_OUT => 'Merma',
        self::TYPE_EMPLOYEE_CONSUMPTION_OUT => 'Mercado de empleados',
        self::TYPE_ADJUSTMENT => 'Ajuste',
    ];

    protected $fillable = [
        'inventory_item_id',
        'type',
        'quantity',
        'unit_cost',
        'balance_after',
        'reason_note',
        'reference_id',
        'reference_type',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_cost' => 'float',
        'balance_after' => 'float',
    ];

    protected $appends = ['type_label'];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute()
    {
        return self::$types[$this->type] ?? $this->type;
    }
}
