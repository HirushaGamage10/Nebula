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
    public function up(): void
    {
        Schema::table('course_registration', function (Blueprint $table) {
            $table->date('course_start_date')->nullable()->after('counselor_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('course_registration', function (Blueprint $table) {
            $table->dropColumn('course_start_date');
        });
    }
};
