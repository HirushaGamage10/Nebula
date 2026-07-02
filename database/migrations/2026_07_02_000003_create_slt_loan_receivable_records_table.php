<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slt_loan_receivable_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_payment_plan_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedInteger('loan_installment_number');
            $table->decimal('total_loan_amount', 12, 2)->default(0);
            $table->unsignedInteger('loan_taken_years')->nullable();
            $table->unsignedInteger('loan_installment_count')->nullable();
            $table->unsignedInteger('apply_from_installment')->nullable();
            $table->decimal('monthly_receivable_amount', 12, 2)->default(0);
            $table->date('payment_effective_date');
            $table->timestamps();

            $table->foreign('student_payment_plan_id')
                ->references('id')
                ->on('student_payment_plans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slt_loan_receivable_records');
    }
};
