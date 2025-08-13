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
        try {
            Schema::table('payment_plans', function (Blueprint $table) {
                $table->dropColumn('course_type');
            });
        } catch (\Exception $e) {
            // If column doesn't exist, ignore the error
            if (strpos($e->getMessage(), "doesn't exist") === false) {
                throw $e;
            }
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
                $table->string('course_type')->after('location');
            });
        } catch (\Exception $e) {
            // If column already exists, ignore the error
            if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                throw $e;
            }
        }
    }
};
