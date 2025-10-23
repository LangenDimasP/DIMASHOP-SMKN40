<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi mass-assignment (gabungkan lama + baru)
    protected $fillable = [
        'code', 'discount', 'valid_until', 'usage_limit', 'used_count', // Kolom lama untuk voucher
        'type', 'image', 'active' // Kolom baru untuk promo produk
    ];

    // Type casting untuk kolom tertentu (otomatis konversi tipe data)
    protected $casts = [
        'active' => 'boolean', // Agar 'active' selalu boolean (true/false)
        'discount' => 'decimal:2', // Agar 'discount' selalu decimal dengan 2 digit desimal
        'valid_until' => 'date', // Agar 'valid_until' selalu objek Carbon/Date
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    // Helper: cek valid promo (untuk voucher lama)
    public function isValid()
    {
        return now() <= $this->valid_until && $this->used_count < $this->usage_limit;
    }

    // Helper baru: cek apakah promo produk aktif (opsional, untuk kemudahan)
    public function isActive()
    {
        return $this->active;
    }
}