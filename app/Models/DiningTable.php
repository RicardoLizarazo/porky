<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */

    protected $table = 'tables';

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'location_id',
        'floor_id',
        'waiter_id',
        'name',
        'capacity',
        'status',
        'uuid',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | ESTADOS
    |--------------------------------------------------------------------------
    */

    const AVAILABLE = 'available';
    const OCCUPIED = 'occupied';
    const RESERVED = 'reserved';
    const CLEANING = 'cleaning';
    const PENDING_PAYMENT = 'pending_payment';

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ORDEN ACTIVA
    |--------------------------------------------------------------------------
    */

    public function activeOrder()
    {
        return $this->hasOne(Order::class, 'table_id')
            ->whereIn('status_id', [
                1, // abierta
                2, // en proceso
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isAvailable(): bool
    {
        return !$this->activeOrder()->exists();
    }

    public function operationalStatus(): string
    {
        /*
        |--------------------------------------------------------------------------
        | SI TIENE ORDEN ACTIVA
        |--------------------------------------------------------------------------
        */

        if ($this->activeOrder()->exists()) {

            return self::OCCUPIED;
        }

        /*
        |--------------------------------------------------------------------------
        | SI NO → USAR ESTADO BASE
        |--------------------------------------------------------------------------
        */

        return $this->status;
    }

    public function canBeAccessedBy($user): bool
    {
        /*
        |--------------------------------------------------------------------------
        | SIN MESERO ASIGNADO
        |--------------------------------------------------------------------------
        */

        if (!$this->waiter_id) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | MISMO MESERO
        |--------------------------------------------------------------------------
        */

        if ((int) $this->waiter_id === (int) $user->id) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / SUPERVISOR
        |--------------------------------------------------------------------------
        */

        if ($user->can('orders.override')) {
            return true;
        }

        return false;
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

    public function scopeAvailable($query)
    {
        return $query->where('status', self::AVAILABLE);
    }
}