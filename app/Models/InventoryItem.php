<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    const TYPE_RAW_MATERIAL = 'raw_material';
    const TYPE_RESALE = 'resale';
    const TYPE_PACKAGING = 'packaging';
    const TYPE_CLEANING = 'cleaning';

    public static array $types = [
        self::TYPE_RAW_MATERIAL => 'Materia prima',
        self::TYPE_RESALE => 'Reventa',
        self::TYPE_PACKAGING => 'Empaque',
        self::TYPE_CLEANING => 'Aseo',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'base_unit_id',
        'stock',
        'average_cost',
        'min_stock',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'stock' => 'float',
        'average_cost' => 'float',
        'min_stock' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = ['type_label', 'value'];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function itemUnits()
    {
        return $this->hasMany(InventoryItemUnit::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_inventory_item')
            ->withPivot('supplier_sku', 'last_price')
            ->withTimestamps();
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereNotNull('min_stock')
            ->whereColumn('stock', '<=', 'min_stock');
    }

    /*
    |--------------------------------------------------------------------------
    | LÓGICA DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    /**
     * Factor de conversión de una unidad hacia la unidad base de este producto.
     * La unidad base siempre tiene factor 1.
     */
    public function factorToBase(int $unitId): float
    {
        if ($unitId == $this->base_unit_id) {
            return 1.0;
        }

        $itemUnit = $this->itemUnits->firstWhere('unit_id', $unitId);

        if (!$itemUnit) {
            throw new \InvalidArgumentException('Esta unidad no está configurada para este producto.');
        }

        return (float) $itemUnit->factor_to_base;
    }

    public function convertToBase(float $quantity, int $unitId): float
    {
        return $quantity * $this->factorToBase($unitId);
    }

    /**
     * Recalcula el costo promedio ponderado al agregar una entrada de stock con costo.
     */
    public function applyWeightedAverage(float $incomingBaseQuantity, float $incomingBaseUnitCost): float
    {
        $currentStock = (float) $this->stock;
        $currentAverage = (float) $this->average_cost;

        $newStock = $currentStock + $incomingBaseQuantity;

        if ($newStock <= 0) {
            return $incomingBaseUnitCost;
        }

        return (($currentStock * $currentAverage) + ($incomingBaseQuantity * $incomingBaseUnitCost)) / $newStock;
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getTypeLabelAttribute()
    {
        return self::$types[$this->type] ?? $this->type;
    }

    public function getValueAttribute()
    {
        return round(((float) $this->stock) * ((float) $this->average_cost), 4);
    }
}
