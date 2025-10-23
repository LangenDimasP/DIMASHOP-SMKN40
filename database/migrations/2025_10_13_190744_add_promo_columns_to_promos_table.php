<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->enum('type', ['buy_x_get_y_free', 'buy_x_for_y'])->nullable()->after('code'); // Tipe promo, nullable agar tidak konflik dengan data lama
            $table->string('image')->nullable()->after('type'); // Path gambar poster
            $table->boolean('active')->default(false)->after('image'); // Status aktif
        });
    }

    public function down()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['type', 'image', 'active']);
        });
    }
};