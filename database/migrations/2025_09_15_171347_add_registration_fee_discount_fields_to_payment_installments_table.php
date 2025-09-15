<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->decimal('registration_fee_discount_applied', 10, 2)->default(0)->after('slt_loan_amount');
            $table->string('registration_fee_discount_note')->nullable()->after('registration_fee_discount_applied');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->dropColumn(['registration_fee_discount_applied', 'registration_fee_discount_note']);
        });
    }
};
