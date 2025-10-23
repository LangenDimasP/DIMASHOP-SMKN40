<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', function ($table) {
            $table->string('voucher_code')->nullable()->after('items');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('voucher_code');
        });
    }

    public function down()
    {
        Schema::table('transactions', function ($table) {
            $table->dropColumn(['voucher_code', 'discount_amount']);
        });
    }
};
