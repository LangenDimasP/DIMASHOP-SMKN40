<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'name',
        'description',
        'code',
        'discount_type',
        'discount_value',
        'min_order',
        'usage_limit',
        'usage_count',
        'expires_at',
        'is_active',
        'points_required', // Baru
        'is_redeemable_with_points', // Baru
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Scope untuk voucher aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function redeemedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_vouchers')->withPivot('redeemed_at');
    }
}