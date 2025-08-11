<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_plan_discounts', function (Blueprint $table) {
            // Drop old FK (works with Laravel's default FK name)
            $table->dropForeign(['payment_plan_id']);

            // Ensure the column type matches parent PK type
            // (Install doctrine/dbal if "change()" errors)
            $table->unsignedBigInteger('payment_plan_id')->change();

            // Add new FK -> student_payment_plans(id)
            $table->foreign('payment_plan_id')
                  ->references('id')->on('student_payment_plans')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_plan_discounts', function (Blueprint $table) {
            $table->dropForeign(['payment_plan_id']);
            $table->unsignedBigInteger('payment_plan_id')->change();

            // Restore old FK -> payment_plans(id)
            $table->foreign('payment_plan_id')
                  ->references('id')->on('payment_plans')
                  ->cascadeOnDelete();
        });
    }
};
