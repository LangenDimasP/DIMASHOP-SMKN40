<?php

namespace App\Models;

// Tambahan: Import untuk Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory;  // Tambahan: Enable factory support
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'po_code',
        'points',
        'dimascash_balance',
        'xp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dimascash_balance' => 'decimal:2',
        ];
    }

    // Relasi Role (dari sebelumnya)
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Helper cek role
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Relasi Transaction (dari sebelumnya)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function notifications()
{
    return $this->hasMany(\App\Models\Notification::class);
}

    // Accessor untuk format Rupiah
    public function getDimascashBalanceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->dimascash_balance, 0, ',', '.');
    }
    public function topUpRequests()
    {
        return $this->hasMany(TopUpRequest::class);
    }
    public function redeemedVouchers()
{
    return $this->belongsToMany(Voucher::class, 'user_vouchers')->withTimestamps();
}
}