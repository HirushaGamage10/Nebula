<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payment_installments', function (Blueprint $t) {
            // keep legacy 'amount' for backward compatibility (base)
            if (!Schema::hasColumn('payment_installments','base_amount')) {
                $t->decimal('base_amount', 12, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payment_installments','discount_amount')) {
                $t->decimal('discount_amount', 12, 2)->default(0)->after('base_amount');
            }
            if (!Schema::hasColumn('payment_installments','discount_note')) {
                $t->string('discount_note')->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('payment_installments','slt_loan_amount')) {
                $t->decimal('slt_loan_amount', 12, 2)->default(0)->after('discount_note');
            }
            if (!Schema::hasColumn('payment_installments','final_amount')) {
                $t->decimal('final_amount', 12, 2)->nullable()->after('slt_loan_amount');
            }
            if (!Schema::hasColumn('payment_installments','paid_date')) {
                $t->date('paid_date')->nullable()->after('status');
            }
        });

        // backfill
        DB::statement("
            UPDATE payment_installments
            SET base_amount   = COALESCE(base_amount, amount),
                discount_amount = COALESCE(discount_amount, 0),
                slt_loan_amount = COALESCE(slt_loan_amount, 0),
                final_amount  = COALESCE(final_amount, amount)
        ");
    }

    public function down(): void {
        Schema::table('payment_installments', function (Blueprint $t) {
            $t->dropColumn(['base_amount','discount_amount','discount_note','slt_loan_amount','final_amount','paid_date']);
        });
    }
};
