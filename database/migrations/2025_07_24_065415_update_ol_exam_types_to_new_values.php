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
        // First, update existing data to a temporary value
        DB::statement("UPDATE student_exams SET ol_exam_type = 'Local' WHERE ol_exam_type = 'London'");
        
        // Drop the enum column
        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropColumn('ol_exam_type');
        });
        
        // Add the new enum column with updated values
        Schema::table('student_exams', function (Blueprint $table) {
            $table->enum('ol_exam_type', ['Local', 'London Cambridge', 'London Edexcel', 'Other'])->nullable()->after('student_id');
        });
        
        // Update the data back to the correct value
        DB::statement("UPDATE student_exams SET ol_exam_type = 'London Cambridge' WHERE ol_exam_type = 'Local' AND student_id IN (SELECT student_id FROM student_exams WHERE ol_exam_type = 'Local')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Update data back to old values
        DB::statement("UPDATE student_exams SET ol_exam_type = 'Local' WHERE ol_exam_type = 'London Cambridge'");
        
        // Drop the new enum column
        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropColumn('ol_exam_type');
        });
        
        // Add back the original enum column
        Schema::table('student_exams', function (Blueprint $table) {
            $table->enum('ol_exam_type', ['Local', 'London', 'International', 'National', 'Regional', 'Global'])->nullable()->after('student_id');
        });
        
        // Update data back
        DB::statement("UPDATE student_exams SET ol_exam_type = 'London' WHERE ol_exam_type = 'Local'");
    }
};
