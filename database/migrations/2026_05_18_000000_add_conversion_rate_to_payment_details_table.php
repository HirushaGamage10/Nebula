<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_details', 'conversion_rate')) {
                $table->decimal('conversion_rate', 12, 6)->nullable()->after('foreign_currency_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            if (Schema::hasColumn('payment_details', 'conversion_rate')) {
                $table->dropColumn('conversion_rate');
            }
        });
    }
};
