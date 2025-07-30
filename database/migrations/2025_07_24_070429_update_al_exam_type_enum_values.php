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
        DB::statement("UPDATE student_exams SET al_exam_type = 'Local' WHERE al_exam_type = 'London'");
        DB::statement("UPDATE student_exams SET al_exam_type = 'Local' WHERE al_exam_type IN ('International', 'National', 'Regional', 'Global')");
        
        // Drop the enum column
        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropColumn('al_exam_type');
        });
        
        // Add the new enum column with updated values
        Schema::table('student_exams', function (Blueprint $table) {
            $table->enum('al_exam_type', ['Local', 'London Cambridge', 'London Edexcel', 'Other'])->nullable()->after('ol_exam_subjects');
        });
        
        // Update the data back to the correct values (keeping as Local for now since we don't have specific mapping)
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Update data back to old values
        DB::statement("UPDATE student_exams SET al_exam_type = 'London' WHERE al_exam_type = 'London Cambridge'");
        DB::statement("UPDATE student_exams SET al_exam_type = 'International' WHERE al_exam_type = 'London Edexcel'");
        DB::statement("UPDATE student_exams SET al_exam_type = 'International' WHERE al_exam_type = 'Other'");
        
        // Drop the new enum column
        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropColumn('al_exam_type');
        });
        
        // Add back the original enum column
        Schema::table('student_exams', function (Blueprint $table) {
            $table->enum('al_exam_type', ['Local', 'London', 'International', 'National', 'Regional', 'Global'])->nullable()->after('ol_exam_subjects');
        });
        
        // Update data back
        DB::statement("UPDATE student_exams SET al_exam_type = 'London' WHERE al_exam_type = 'London Cambridge'");
        DB::statement("UPDATE student_exams SET al_exam_type = 'International' WHERE al_exam_type IN ('London Edexcel', 'Other')");
    }
};
