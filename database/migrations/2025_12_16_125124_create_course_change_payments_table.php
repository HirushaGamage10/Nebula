<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_change_payments')) {
            Schema::create('course_change_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('old_course_id');
                $table->unsignedBigInteger('old_intake_id')->nullable();
                $table->unsignedBigInteger('old_payment_plan_id');
                $table->decimal('total_paid_amount', 12, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->timestamps();

                // Indexes
                $table->index(['student_id', 'old_course_id']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_change_payments');
    }
};