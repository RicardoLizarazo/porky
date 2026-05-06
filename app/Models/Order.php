<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'customer_id',
        'user_id',
        'delivery_user_id',
        'type_id',
        'status_id',
        'payment_method',
        'total_items',
        'subtotal',
        'total',
        'delivery_cost',
        'packaging_total',
        'indication',
        'comment',
        'ordered_at',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'subtotal'   => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Cliente (frontend)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Usuario admin que gestionó el pedido
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔥 Domiciliario asignado
    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }

    // Detalles del pedido
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Estado del pedido
    public function status()
    {
        return $this->belongsTo(StatusOrder::class);
    }

    // Tipo de pedido
    public function type()
    {
        return $this->belongsTo(TypeOrder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (consultas reutilizables)
    |--------------------------------------------------------------------------
    */

    public function scopeSearch($query, $term)
    {
        if (!$term) return $query;

        return $query->whereHas('customer', function ($q) use ($term) {
            $q->where('name', 'like', "%$term%");
        });
    }

    public function scopeStatus($query, $statusId)
    {
        if (!$statusId) return $query;

        return $query->where('status_id', $statusId);
    }

    public function scopeNew($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'nuevo');
        });
    }

    public function newQuery()
    {
        $query = parent::newQuery();

        $query->select([
            'orders.id',
            'orders.customer_id',
            'orders.user_id',
            'orders.delivery_user_id',
            'orders.type_id',
            'orders.status_id',
            'orders.payment_method',
            'orders.total_items',
            'orders.subtotal',
            'orders.total',
            'orders.delivery_cost',
            'orders.packaging_total',
            'orders.indication',
            'orders.comment',
            'orders.ordered_at',
            'orders.created_at',
            'orders.updated_at',
        ]);

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORES (helpers)
    |--------------------------------------------------------------------------
    */

    // Total formateado
    public function getTotalFormattedAttribute()
    {
        return number_format($this->total, 2);
    }

    // Estado con color (útil para badges)
    public function getStatusBadgeAttribute()
    {
        return [
            'name'  => $this->status->name ?? '',
            'color' => $this->status->color ?? 'secondary',
        ];
    }

    // Nombre del domiciliario
    public function getDeliveryNameAttribute()
    {
        return $this->delivery?->name ?? 'Sin asignar';
    }
}