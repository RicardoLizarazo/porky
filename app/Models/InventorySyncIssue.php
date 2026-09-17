<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySyncIssue extends Model
{
    const REASON_UNLINKED_PRODUCT = 'unlinked_product';

    protected $fillable = [
        'product_id',
        'reason',
        'occurrences',
        'last_order_detail_id',
        'last_seen_at',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'occurrences' => 'integer',
        'last_seen_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function lastOrderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'last_order_detail_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopePending($query)
    {
        return $query->whereNull('resolved_at');
    }
}
