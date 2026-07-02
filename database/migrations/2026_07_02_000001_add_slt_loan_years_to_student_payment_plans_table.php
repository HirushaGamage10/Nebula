<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('student_payment_plans', 'slt_loan_years')) {
                $table->unsignedInteger('slt_loan_years')->nullable()->after('slt_loan_start_installment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            if (Schema::hasColumn('student_payment_plans', 'slt_loan_years')) {
                $table->dropColumn('slt_loan_years');
            }
        });
    }
};
