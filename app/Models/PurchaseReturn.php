<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseReturn extends Model
{
    use LogsActivity;

    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'return_date',
        'reason',
        'credit_note_number',
        'total',
        'created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('purchase_return')
            ->logOnly(['purchase_id', 'supplier_id', 'return_date', 'total'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
