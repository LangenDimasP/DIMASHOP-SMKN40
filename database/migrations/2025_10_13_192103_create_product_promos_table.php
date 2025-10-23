<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_promos', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['buy_x_get_y_free', 'buy_x_for_y'])->unique(); // Tipe promo, unik agar tidak duplikat
            $table->string('image')->nullable(); // Path gambar poster
            $table->boolean('active')->default(false); // Status aktif
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_promos');
    }
};