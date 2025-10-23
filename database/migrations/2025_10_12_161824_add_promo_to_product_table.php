<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('promo_type', ['buy_x_get_y_free', 'buy_x_for_y'])->nullable();
            $table->integer('promo_buy')->nullable(); // x
            $table->integer('promo_get')->nullable(); // y (free atau price)
            $table->boolean('promo_active')->default(false);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['promo_type', 'promo_buy', 'promo_get', 'promo_active']);
        });
    }
};