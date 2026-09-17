<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model
{
    use LogsActivity;

    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'purchase_date',
        'status',
        'total',
        'notes',
        'confirmed_at',
        'confirmed_by',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total' => 'float',
        'confirmed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /*
    |--------------------------------------------------------------------------
    | AUDITORÍA
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('purchase')
            ->logOnly(['supplier_id', 'invoice_number', 'purchase_date', 'status', 'total'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
