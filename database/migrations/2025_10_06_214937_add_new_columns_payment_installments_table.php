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
        $table->enum('installment_type', ['local', 'international', 'mixed'])->nullable()->after('amount');
        $table->decimal('international_amount', 12, 2)->nullable()->after('installment_type');
        $table->string('international_currency', 10)->nullable()->after('international_amount');
        $table->decimal('exchange_rate', 12, 4)->nullable()->after('international_currency');
    });
}

public function down()
{
    Schema::table('payment_installments', function (Blueprint $table) {
        $table->dropColumn(['installment_type', 'international_amount', 'international_currency', 'exchange_rate']);
    });
}

};
