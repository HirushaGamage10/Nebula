<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Add the new column after 'location'
            $table->unsignedBigInteger('course_id')->after('location')->nullable();

            // (Optional) Add foreign key if related to `courses` table
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Drop the foreign key and column if rollback
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
