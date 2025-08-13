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
        Schema::table('courses', function (Blueprint $table) {
            // Check if course_type column doesn't exist before adding it
            if (!Schema::hasColumn('courses', 'course_type')) {
                $table->enum('course_type', ['degree', 'certificate'])->default('degree')->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Check if course_type column exists before dropping it
            if (Schema::hasColumn('courses', 'course_type')) {
                $table->dropColumn('course_type');
            }
        });
    }
};
