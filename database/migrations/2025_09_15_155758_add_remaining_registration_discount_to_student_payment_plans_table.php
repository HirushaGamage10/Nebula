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
        Schema::table('student_payment_plans', function (Blueprint $table) {
            $table->decimal('remaining_registration_discount', 10, 2)->default(0)->after('final_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_payment_plans', function (Blueprint $table) {
            $table->dropColumn('remaining_registration_discount');
        });
    }
};
