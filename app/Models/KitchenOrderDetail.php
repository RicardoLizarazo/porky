<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenOrderDetail extends Model
{
    protected $fillable = [

        'kitchen_order_id',
        'order_detail_id',
        'kitchen_station_id',
        'product_name',
        'quantity',
        'price',
        'subtotal',
        'comment'
    ];

    public function kitchenOrder()
    {
        return $this->belongsTo(
            KitchenOrder::class
        );
    }

    public function station()
    {
        return $this->belongsTo(
            KitchenStation::class,
            'kitchen_station_id'
        );
    }
}
