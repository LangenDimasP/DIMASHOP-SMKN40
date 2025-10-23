<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPromo extends Model
{
    use HasFactory;

    protected $table = 'product_promos'; // Pastikan nama tabel benar

    protected $fillable = ['type', 'image', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Helper: cek apakah promo aktif
    public function isActive()
    {
        return $this->active;
    }
}