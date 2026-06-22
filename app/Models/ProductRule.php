<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRule extends Model
{
    protected $fillable = [

        'name',
        'description',
        'extra_price',
        'is_active',
    ];

    protected $casts = [

        'extra_price' => 'decimal:2',
        'is_active'   => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class,
            'product_rule_product'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}