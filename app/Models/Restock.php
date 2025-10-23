<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    protected $fillable = ['product_id', 'quantity'];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Tambahkan event untuk update stok otomatis
    protected static function booted()
    {
        static::created(function ($restock) {
            $restock->product->increment('stock', $restock->quantity);
        });

        // Opsional: Jika restock dihapus, kurangi stok (untuk rollback)
        static::deleted(function ($restock) {
            $restock->product->decrement('stock', $restock->quantity);
        });
    }
}