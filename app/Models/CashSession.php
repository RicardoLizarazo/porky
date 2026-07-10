<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'cash_register_id',
        'user_id',

        'opened_by',
        'closed_by',

        'opening_amount',
        'closing_amount',

        'opening_notes',
        'closing_notes',

        'opened_at',
        'closed_at',

        'is_open',
    ];

    protected $casts = [

        'opening_amount' => 'decimal:2',
        'closing_amount' => 'decimal:2',

        'opened_at' => 'datetime',
        'closed_at' => 'datetime',

        'is_open' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function cashRegister()
    {
        return $this->belongsTo(
            CashRegister::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function orders()
    {
        return $this->hasMany(
            Order::class
        );
    }

    public function payments()
    {
        return $this->hasMany(
            CashPayment::class
        );
    }

    public function openedBy()
    {
        return $this->belongsTo(
            User::class,
            'opened_by'
        );
    }

    public function closedBy()
    {
        return $this->belongsTo(
            User::class,
            'closed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isClosed(): bool
    {
        return !$this->is_open;
    }

    public static function current()
    {
        return static::query()

            ->open()

            ->where(
                'user_id',
                auth()->id()
            )

            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | TOTALES DEL TURNO
    |--------------------------------------------------------------------------
    */

    public function getSalesTotalAttribute()
    {
        return $this->payments()
            ->sum('amount');
    }

    public function getCashSalesAttribute()
    {
        return $this->payments()

            ->where('payment_method', 'Efectivo')

            ->sum('amount');
    }

    public function getNequiSalesAttribute()
    {
        return $this->payments()

            ->where('payment_method', 'Nequi')

            ->sum('amount');
    }

    public function getQrSalesAttribute()
    {
        return $this->payments()

            ->where('payment_method', 'QR')

            ->sum('amount');
    }

    public function getDaviplataSalesAttribute()
    {
        return $this->payments()

            ->where('payment_method', 'Daviplata')

            ->sum('amount');
    }

    public function getCashTotalAttribute()
    {
        return $this->payments()

            ->where(
                'payment_method',
                'Efectivo'
            )

            ->sum('amount');
    }

    public function getNequiTotalAttribute()
    {
        return $this->payments()

            ->where(
                'payment_method',
                'Nequi'
            )

            ->sum('amount');
    }

    public function getDaviplataTotalAttribute()
    {
        return $this->payments()

            ->where(
                'payment_method',
                'Daviplata'
            )

            ->sum('amount');
    }

    public function getQrTotalAttribute()
    {
        return $this->payments()

            ->where(
                'payment_method',
                'QR'
            )

            ->sum('amount');
    }

    public function getOrdersCountAttribute()
    {
        return $this->orders()

            ->paid()

            ->count();
    }

    public function getInvoicesRequestedAttribute()
    {
        return $this->orders()

            ->where(
                'invoice_requested',
                true
            )

            ->count();
    }

    public function getAverageTicketAttribute()
    {
        if ($this->orders_count == 0) {

            return 0;
        }

        return $this->sales_total / $this->orders_count;
    }

    public function getExpectedCashAttribute()
    {
        return

            $this->opening_amount

            + $this->cash_total;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->where(
            'is_open',
            true
        );
    }
}