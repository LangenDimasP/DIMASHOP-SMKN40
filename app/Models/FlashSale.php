<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_percent',
        'start_time',
        'end_time',
        'day_of_week', // Opsional: misal 'monday', 'tuesday', dll. Jika kosong, berlaku setiap hari
        'active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'active' => 'boolean',
    ];

    // Relasi many-to-many ke Products (untuk multiple produk)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products');
    }

    // Scope untuk flash sale aktif berdasarkan waktu sekarang
    // Catatan: Jika perlu cek per produk, panggil di controller/query
    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->when(request('day_of_week'), function ($q) {
                $q->where('day_of_week', strtolower(now()->format('l')));
            });
    }
}