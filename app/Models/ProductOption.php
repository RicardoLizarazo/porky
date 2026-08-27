<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}