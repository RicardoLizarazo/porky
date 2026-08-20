<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableMerge extends Model
{
    protected $fillable = [
        'primary_table_id', 'secondary_table_id',
        'primary_order_id', 'absorbed_order_id',
        'created_by', 'closed_at',
    ];
    protected $casts = ['closed_at' => 'datetime'];

    public function primaryTable()   { return $this->belongsTo(DiningTable::class, 'primary_table_id'); }
    public function secondaryTable() { return $this->belongsTo(DiningTable::class, 'secondary_table_id'); }
    public function primaryOrder()   { return $this->belongsTo(Order::class, 'primary_order_id'); }
    public function absorbedOrder()  { return $this->belongsTo(Order::class, 'absorbed_order_id'); }

    public function scopeActive($q) { return $q->whereNull('closed_at'); }
}
