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
    Schema::table('payment_details', function (Blueprint $table) {
        $table->decimal('registration_fee_discount_applied', 10, 2)->default(0)->after('discount_amount');
        $table->string('registration_fee_discount_note')->nullable()->after('registration_fee_discount_applied');
    });
}

public function down()
{
    Schema::table('payment_details', function (Blueprint $table) {
        $table->dropColumn(['registration_fee_discount_applied', 'registration_fee_discount_note']);
    });
}

};
