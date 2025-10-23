<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->integer('points_required')->nullable()->after('usage_count'); // Berapa points untuk redeem
            $table->boolean('is_redeemable_with_points')->default(false)->after('points_required'); // Apakah bisa diredeem dengan points
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['points_required', 'is_redeemable_with_points']);
        });
    }
};