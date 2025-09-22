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
        Schema::table('student_payment_plans', function (Blueprint $table) {
            // Store multiple normal discounts
            $table->json('discounts')->nullable()->after('final_amount');

            // Store registration fee discount separately
            $table->json('registration_fee_discount')->nullable()->after('discounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            $table->dropColumn(['discounts', 'registration_fee_discount']);
        });
    }
};
