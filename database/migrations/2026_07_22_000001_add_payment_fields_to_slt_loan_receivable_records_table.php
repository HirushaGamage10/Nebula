<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('slt_loan_receivable_records')) {
            Schema::table('slt_loan_receivable_records', function (Blueprint $table) {
                if (!Schema::hasColumn('slt_loan_receivable_records', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('payment_effective_date');
                }
                if (!Schema::hasColumn('slt_loan_receivable_records', 'receipt_no')) {
                    $table->string('receipt_no')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('slt_loan_receivable_records', 'status')) {
                    $table->string('status')->default('pending')->after('receipt_no');
                }
                if (!Schema::hasColumn('slt_loan_receivable_records', 'remarks')) {
                    $table->text('remarks')->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('slt_loan_receivable_records')) {
            Schema::table('slt_loan_receivable_records', function (Blueprint $table) {
                $columns = ['payment_method', 'receipt_no', 'status', 'remarks'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('slt_loan_receivable_records', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
