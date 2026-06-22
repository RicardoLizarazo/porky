<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenStation extends Model
{
    protected $fillable = [

        'name',
        'color',
        'is_active'
    ];

    public function categories()
    {
        return $this->hasMany(
            Category::class
        );
    }
}
