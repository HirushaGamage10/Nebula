<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('payment_plans', 'intake_id')) {
            Schema::table('payment_plans', function (Blueprint $table) {
                $table->unsignedBigInteger('intake_id')->after('course_id');
                $table->foreign('intake_id')->references('intake_id')->on('intakes')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::table('payment_plans', function (Blueprint $table) {
                $table->dropForeign(['intake_id']);
                $table->dropColumn('intake_id');
            });
        } catch (\Exception $e) {
            // Ignore errors during rollback
        }
    }
};
