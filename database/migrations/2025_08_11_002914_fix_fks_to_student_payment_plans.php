<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // payment_installments → student_payment_plans(id)
        Schema::table('payment_installments', function (Blueprint $table) {
            // If this line errors, replace with the exact constraint name you see in SHOW CREATE TABLE
            $table->dropForeign(['payment_plan_id']);

            // Ensure same type as student_payment_plans.id
            $table->unsignedBigInteger('payment_plan_id')->change();

            $table->foreign('payment_plan_id')
                  ->references('id')->on('student_payment_plans')
                  ->cascadeOnDelete();
        });

        // payment_plan_discounts → student_payment_plans(id)
        Schema::table('payment_plan_discounts', function (Blueprint $table) {
            $table->dropForeign(['payment_plan_id']);
            $table->unsignedBigInteger('payment_plan_id')->change();

            $table->foreign('payment_plan_id')
                  ->references('id')->on('student_payment_plans')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->dropForeign(['payment_plan_id']);
            $table->unsignedBigInteger('payment_plan_id')->change();
            $table->foreign('payment_plan_id')
                  ->references('id')->on('payment_plans')
                  ->cascadeOnDelete();
        });

        Schema::table('payment_plan_discounts', function (Blueprint $table) {
            $table->dropForeign(['payment_plan_id']);
            $table->unsignedBigInteger('payment_plan_id')->change();
            $table->foreign('payment_plan_id')
                  ->references('id')->on('payment_plans')
                  ->cascadeOnDelete();
        });
    }
};
