<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\ProfitSetting;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',  // Tambah ini
        'description',
        'category',
        'selling_price',  // Ini accessor, bukan kolom DB
        'discount_value',
        'discount_type',
        'final_price',  // Ini accessor, bukan kolom DB
        'stock',
        'image',
        'images',
        'unique_code',
        'promo_type',
        'promo_buy',
        'promo_get',
        'promo_active',
    ];

    protected $casts = [
        'images' => 'array',  // Cast sebagai array
        'promo_active' => 'boolean',
    ];

    protected $appends = ['selling_price', 'final_price'];  // Tambah ini untuk include accessor ke JSON

    // Hapus boot method agar unique_code dihandle di controller

    // Harga jual setelah keuntungan
    public function getSellingPriceAttribute()
    {
        $profit = ProfitSetting::first();
        if (!$profit)
            return $this->price;

        if ($profit->type == 'percent') {
            return $this->price * (1 + $profit->value / 100);
        } elseif ($profit->type == 'fixed') {
            return $this->price + $profit->value;
        }
        return $this->price;
    }

    public function restocks()
    {
        return $this->hasMany(Restock::class);
    }


    // Harga akhir setelah diskon
    public function getFinalPriceAttribute()
    {
        $sellingPrice = $this->selling_price; // Sudah termasuk margin

        // Diskon produk dulu (jika ada)
        if ($this->discount_value > 0) {
            if ($this->discount_type == 'percent') {
                $sellingPrice -= ($sellingPrice * $this->discount_value / 100);
            } elseif ($this->discount_type == 'fixed') {
                $sellingPrice -= $this->discount_value;
            }
        }

        // Baru cek flash sale (jika masih aktif, override)
        $now = now();
        $activeFlashSale = $this->flashSales()
            ->where('active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        if ($activeFlashSale) {
            // Override dengan harga flash sale (dari harga asli atau setelah diskon?)
            $sellingPrice = $this->selling_price * (1 - $activeFlashSale->discount_percent / 100); // Override total
        }

        return max(0, $sellingPrice);
    }
    public function flashSales()
    {
        return $this->belongsToMany(\App\Models\FlashSale::class, 'flash_sale_products', 'product_id', 'flash_sale_id');
    }
    public function flashSale()
    {
        return $this->flashSales();
    }
}