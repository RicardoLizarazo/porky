<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'schedule'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'schedule' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('is_available')
            ->withTimestamps();
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /*
    |--------------------------------------------------------------------------
    | LÓGICA DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    public function isOpenNow(): bool
    {
        if (!$this->is_active || !$this->schedule) {
            return false;
        }

        $now = Carbon::now();

        // 👇 IMPORTANTE: mismo formato que tu JSON
        $day = strtolower($now->format('l')); // monday, tuesday...

        $daySchedule = $this->schedule[$day] ?? null;

        if (!$daySchedule || !$daySchedule['active']) {
            return false;
        }

        $start = Carbon::createFromTimeString($daySchedule['start']);
        $end = Carbon::createFromTimeString($daySchedule['end']);

        return $now->between($start, $end);
    }
}