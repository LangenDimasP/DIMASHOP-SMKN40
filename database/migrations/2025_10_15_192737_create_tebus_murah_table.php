<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('tebus_murah', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id'); // Produk yang ditebus murah
        $table->integer('tebus_price'); // Harga tebus murah
        $table->integer('min_order')->default(0); // Minimal belanja (misal 50000)
        $table->integer('max_qty')->default(1); // Maksimal qty tebus murah per transaksi
        $table->boolean('active')->default(true);
        $table->timestamps();

        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tebus_murah');
    }
};
