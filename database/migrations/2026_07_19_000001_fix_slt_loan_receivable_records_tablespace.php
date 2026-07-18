<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Try dropping the table first, in case of InnoDB tablespace corruption.
        try {
            DB::statement('DROP TABLE IF EXISTS slt_loan_receivable_records');
        } catch (\Throwable $e) {
            \Log::warning('Migration: Failed to drop slt_loan_receivable_records during tablespace fix attempt: ' . $e->getMessage());
        }

        // Recreate table if not exists
        if (!Schema::hasTable('slt_loan_receivable_records')) {
            try {
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
            } catch (\Throwable $e) {
                \Log::error('Migration: Failed to recreate slt_loan_receivable_records: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slt_loan_receivable_records');
    }
};
