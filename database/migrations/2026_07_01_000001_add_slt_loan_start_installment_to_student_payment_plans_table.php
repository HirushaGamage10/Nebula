<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('student_payment_plans', 'slt_loan_start_installment')) {
                $table->unsignedInteger('slt_loan_start_installment')->nullable()->after('slt_loan_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            if (Schema::hasColumn('student_payment_plans', 'slt_loan_start_installment')) {
                $table->dropColumn('slt_loan_start_installment');
            }
        });
    }
};
