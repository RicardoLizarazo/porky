<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
    const CASH = 'cash';
    const NEQUI = 'nequi';
    const DAVIPLATA = 'daviplata';
    const QR = 'qr';

    protected $fillable = [

        'order_id',
        'cash_session_id',
        'cash_register_id',
        'user_id',

        'payment_method',
        'amount',

        'reference',
        'notes',
    ];

    protected $casts = [

        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }

    public function cashSession()
    {
        return $this->belongsTo(
            CashSession::class
        );
    }

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
}