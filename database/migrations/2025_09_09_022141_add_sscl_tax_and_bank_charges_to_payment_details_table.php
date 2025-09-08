<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->decimal('sscl_tax_amount', 10, 2)
                  ->nullable()
                  ->default(0)
                  ->comment('SSCL tax applied on franchise fee');

            $table->decimal('bank_charges', 10, 2)
                  ->nullable()
                  ->default(0)
                  ->comment('Bank charges applied on franchise fee');
        });
    }

    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('sscl_tax_amount');
            $table->dropColumn('bank_charges');
        });
    }
};
