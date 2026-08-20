<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'kitchen_station_id',
        'is_active',
        'is_visible',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function kitchenStation()
    {
        return $this->belongsTo(
            KitchenStation::class
        );
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
}