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
        Schema::table('timetable', function (Blueprint $table) {
            // First add the subject_id column
            $table->unsignedBigInteger('subject_id')->after('id'); // adjust position if needed

            // Then add the foreign key constraint
            $table->foreign('subject_id')
                  ->references('module_id')
                  ->on('modules')
                  ->onDelete('cascade'); // optional, but good for cleanup
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timetable', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
