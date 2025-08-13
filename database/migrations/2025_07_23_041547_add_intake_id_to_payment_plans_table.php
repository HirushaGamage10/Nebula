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
        try {
            Schema::table('payment_plans', function (Blueprint $table) {
                $table->unsignedBigInteger('intake_id')->after('course_id');
                $table->foreign('intake_id')->references('intake_id')->on('intakes')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // If column already exists, just add the foreign key if it doesn't exist
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                try {
                    Schema::table('payment_plans', function (Blueprint $table) {
                        $table->foreign('intake_id')->references('intake_id')->on('intakes')->onDelete('cascade');
                    });
                } catch (\Exception $fkException) {
                    // Foreign key might already exist, ignore
                }
            } else {
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
                $table->dropForeign(['intake_id']);
                $table->dropColumn('intake_id');
            });
        } catch (\Exception $e) {
            // Ignore errors during rollback
        }
    }
};
