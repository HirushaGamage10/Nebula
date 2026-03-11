<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('semester_format', ['numerical', 'alphabetical'])
                  ->default('numerical')
                  ->after('course_type')
                  ->comment('Display semesters as numerical (1,2,3) or alphabetical (A,B,C)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('semester_format');
        });
    }
};
