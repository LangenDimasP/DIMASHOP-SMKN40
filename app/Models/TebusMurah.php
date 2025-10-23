<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TebusMurah extends Model
{
    protected $table = 'tebus_murah';
    protected $fillable = ['product_id', 'tebus_price', 'min_order', 'max_qty', 'active'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}