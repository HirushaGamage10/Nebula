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
        Schema::table('payment_details', function (Blueprint $table) {
            $table->decimal('late_fee', 10, 2)->default(0)->after('amount');
            $table->decimal('approved_late_fee', 10, 2)->default(0)->after('late_fee');
            $table->decimal('total_fee', 12, 2)->default(0)->after('approved_late_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn(['late_fee', 'approved_late_fee', 'total_fee']);
        });
    }
};
