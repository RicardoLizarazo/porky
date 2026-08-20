<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenStation extends Model
{
    protected $fillable = [
        'name',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'kitchen_station_product');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}