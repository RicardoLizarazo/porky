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

    public function mergeAsPrimary()
    {
        return $this->hasOne(TableMerge::class, 'primary_table_id')->whereNull('closed_at');
    }

    public function mergeAsSecondary()
    {
        return $this->hasOne(TableMerge::class, 'secondary_table_id')->whereNull('closed_at');
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
        | ADMIN / SUPERVISOR (override completo)
        |--------------------------------------------------------------------------
        */
 
        if ($user->can('orders.override')) {
            return true;
        }
 
        /*
        |--------------------------------------------------------------------------
        | AGREGADO RÁPIDO (ej. Cajero que también es Mesero, agrega un
        | producto puntual a una mesa que no es la suya, sin necesitar
        | el override completo de administrador)
        |--------------------------------------------------------------------------
        */
 
        if ($user->can('floor_map.add_product')) {
            return true;
        }
 
        return false;
    }


    /** Mesa efectiva de la orden: la propia, o la de la mesa principal si está unida como secundaria */
    public function effectiveOrder(): ?Order
    {
        if ($this->activeOrder) {
            return $this->activeOrder;
        }
    
        $merge = $this->mergeAsSecondary ?? $this->mergeAsSecondary()->first();
    
        return $merge?->primaryTable?->activeOrder;
    }

    /** Libera la mesa y, si estaba unida, también la mesa hermana. Úsalo donde hoy cierras/pagas la orden. */
    public function releaseWithMerges(): void
    {
        $merge = $this->mergeAsPrimary()->first() ?? $this->mergeAsSecondary()->first();
    
        if ($merge) {
            self::whereIn('id', [$merge->primary_table_id, $merge->secondary_table_id])
                ->update(['status' => self::AVAILABLE, 'waiter_id' => null]);
    
            $merge->update(['closed_at' => now()]);
        } else {
            $this->update(['status' => self::AVAILABLE, 'waiter_id' => null]);
        }
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