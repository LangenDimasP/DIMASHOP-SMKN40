<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
        {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('unique_code')->unique();  // TRANSAKSI-XXXX
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');  // User PO
                $table->foreignId('kasir_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('status', ['pending', 'dibayar', 'selesai'])->default('pending');
                $table->string('payment_method');
                $table->decimal('total_price', 10, 2);
                $table->json('items');  // Array produk yang dibeli
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
