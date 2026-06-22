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

        /*
        |--------------------------------------------------------------------------
        | RELACIONES
        |--------------------------------------------------------------------------
        */

        'customer_id',
        'user_id',
        'delivery_user_id',

        'location_id',
        'floor_id',
        'table_id',

        'cash_register_id',
        'cash_session_id',

        'type_id',
        'sales_channel_id',
        'service_type_id',

        'status_id',

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS / PAGOS
        |--------------------------------------------------------------------------
        */

        'uuid',
        'document_number',

        'payment_method',

        'is_paid',
        'paid_at',

        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        'total_items',

        'subtotal',
        'tax',
        'discount',

        'delivery_cost',
        'packaging_total',
        'tip',

        'total',

        /*
        |--------------------------------------------------------------------------
        | OBSERVACIONES
        |--------------------------------------------------------------------------
        */

        'indication',
        'comment',

        /*
        |--------------------------------------------------------------------------
        | FECHAS
        |--------------------------------------------------------------------------
        */

        'ordered_at',
        'closed_at',
    ];

    protected $attributes = [
        'subtotal' => 0,
        'tax' => 0,
        'discount' => 0,
        'delivery_cost' => 0,
        'packaging_total' => 0,
        'tip' => 0,
        'total' => 0,

        'total_items' => 0,

        'is_paid' => false,
    ];

    protected $casts = [

        /*
        |--------------------------------------------------------------------------
        | BOOLEANOS
        |--------------------------------------------------------------------------
        */

        'is_paid' => 'boolean',

        /*
        |--------------------------------------------------------------------------
        | FECHAS
        |--------------------------------------------------------------------------
        */

        'ordered_at' => 'datetime',
        'paid_at'    => 'datetime',
        'closed_at'  => 'datetime',

        /*
        |--------------------------------------------------------------------------
        | DECIMALES
        |--------------------------------------------------------------------------
        */

        'subtotal'        => 'decimal:2',
        'tax'             => 'decimal:2',
        'discount'        => 'decimal:2',
        'delivery_cost'   => 'decimal:2',
        'packaging_total' => 'decimal:2',
        'tip'             => 'decimal:2',
        'total'           => 'decimal:2',
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

    // Domiciliario asignado
    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }

    // Sede
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Piso
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    // Mesa
    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class, 'table_id');
    }

    // Caja
    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    // Sesión de caja
    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }

    // Canal de venta
    public function salesChannel()
    {
        return $this->belongsTo(SalesChannel::class);
    }

    // Tipo de servicio
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
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
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {

            $q->where('document_number', 'like', "%{$term}%")

                ->orWhereHas('customer', function ($customer) use ($term) {
                    $customer->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function scopeStatus($query, $statusId)
    {
        if (!$statusId) {
            return $query;
        }

        return $query->where('status_id', $statusId);
    }

    public function scopeLocation($query, $locationId)
    {
        if (!$locationId) {
            return $query;
        }

        return $query->where('location_id', $locationId);
    }

    public function scopePendingPayment($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeNew($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'nuevo');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | NEW QUERY
    |--------------------------------------------------------------------------
    */

    public function newQuery()
    {
        $query = parent::newQuery();

        $query->select([

            'orders.id',
            'orders.uuid',

            'orders.customer_id',
            'orders.user_id',
            'orders.delivery_user_id',

            'orders.location_id',
            'orders.floor_id',
            'orders.table_id',

            'orders.cash_register_id',
            'orders.cash_session_id',

            'orders.type_id',
            'orders.sales_channel_id',
            'orders.service_type_id',

            'orders.status_id',

            'orders.document_number',

            'orders.payment_method',

            'orders.is_paid',
            'orders.paid_at',

            'orders.total_items',

            'orders.subtotal',
            'orders.tax',
            'orders.discount',

            'orders.delivery_cost',
            'orders.packaging_total',
            'orders.tip',

            'orders.total',

            'orders.indication',
            'orders.comment',

            'orders.ordered_at',
            'orders.closed_at',

            'orders.created_at',
            'orders.updated_at',
        ]);

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORES
    |--------------------------------------------------------------------------
    */

    // Total formateado
    public function getTotalFormattedAttribute()
    {
        return number_format($this->total, 2);
    }

    // Estado con color
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

    // Estado pago
    public function getPaymentStatusAttribute()
    {
        return $this->is_paid
            ? 'Pagado'
            : 'Pendiente';
    }
}