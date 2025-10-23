<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Change to DECIMAL for currency/amounts (adjust precision as needed)
            $table->decimal('discount_value', 10, 2)->nullable()->change();  // Allows up to 99999999.99
            // Or for integers: $table->integer('discount_value')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert if needed (e.g., back to TINYINT)
            $table->tinyInteger('discount_value')->nullable()->change();
        });
    }
};