<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;
//use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /** Nombre del guard de autenticación */
    protected $guard_name = 'sanctum';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function responsibleCashRegisters()
    {
        return $this->hasMany(CashRegister::class,'responsible_user_id');
    }

    public function cashSessions()
    {
        return $this->hasMany(
            CashSession::class
        );
    }

    public function openCashSession()
    {
        return $this->hasOne(
            CashSession::class,
            'user_id'
        )->where('is_open', true);
    }

    public function openedCashSessions()
    {
        return $this->hasMany(
            CashSession::class,
            'opened_by'
        );
    }

    public function closedCashSessions()
    {
        return $this->hasMany(
            CashSession::class,
            'closed_by'
        );
    }

    public function cashPayments()
    {
        return $this->hasMany(
            CashPayment::class
        );
    }

    /* ============================================================
     * = AUDITORÍA SPATIE
     * ============================================================
     */

    /**
     * Configuración de auditoría con Spatie Activitylog.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly([
                'name',
                'email',
                'employee_code',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
