<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('student_payment_plans', 'slt_receivable_effective_date')) {
                $table->date('slt_receivable_effective_date')->nullable()->after('slt_loan_years');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            if (Schema::hasColumn('student_payment_plans', 'slt_receivable_effective_date')) {
                $table->dropColumn('slt_receivable_effective_date');
            }
        });
    }
};
