<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropForeign(['product_id']); // Hapus foreign key jika ada
            $table->dropColumn('product_id'); // Hapus kolom
        });
    }

    public function down()
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products'); // Restore jika rollback
        });
    }
};