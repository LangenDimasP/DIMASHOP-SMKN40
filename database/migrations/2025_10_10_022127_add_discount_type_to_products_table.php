<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('discount_type', ['fixed', 'percent'])->default('percent')->after('discount');
            $table->renameColumn('discount', 'discount_value'); // Rename discount ke discount_value
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->renameColumn('discount_value', 'discount'); // Rollback jika perlu
        });
    }
};