<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenOrder extends Model
{
    protected $fillable = [
        'order_id',
        'floor_id',
        'table_id',
        'status',
        'sent_at',
        'ready_at',
        'delivered_at',
    ];

    protected $casts = [

        'sent_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function diningTable()
    {
        return $this->belongsTo(
            DiningTable::class,
            'table_id'
        );
    }

    public function details()
    {
        return $this->hasMany(
            KitchenOrderDetail::class
        );
    }
}
