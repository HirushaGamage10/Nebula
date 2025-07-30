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
        Schema::table('intakes', function (Blueprint $table) {
            $table->decimal('sscl_tax', 5, 2)->default(0.00)->after('franchise_payment_currency');
            $table->decimal('bank_charges', 12, 2)->default(0.00)->after('sscl_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            $table->dropColumn(['sscl_tax', 'bank_charges']);
        });
    }
};
