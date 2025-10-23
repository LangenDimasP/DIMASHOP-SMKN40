<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
        'unique_code',
        'user_id',
        'kasir_id',
        'status',
        'payment_method',
        'total_price',
        'cash_amount', // Tambah ini untuk menyimpan jumlah bayar tunai
        'items',
        'voucher_code',     
        'discount_amount',
        'points_used',   
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transaction) {
            $transaction->unique_code = 'TRANSAKSI-' . Str::random(8);
        });
    }
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    // Tambah accessor untuk kembalian (opsional, tapi berguna)
    public function getChangeAttribute()
    {
        if ($this->payment_method == 'cash' && $this->cash_amount && $this->cash_amount > $this->total_price) {
            return $this->cash_amount - $this->total_price;
        }
        return 0;
    }
}