<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_exams', function (Blueprint $table) {
            $table->id('exam_id');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');

            // O/L fields
            $table->enum('ol_exam_type', ['Local', 'London', 'International', 'National', 'Regional', 'Global'])->nullable();
            $table->string('ol_exam_year')->nullable();
            $table->string('ol_index_no')->nullable();
            $table->text('ol_certificate')->nullable();
            $table->json('ol_exam_subjects')->nullable();

            // A/L fields
            $table->enum('al_exam_type', ['Local', 'London', 'International', 'National', 'Regional', 'Global'])->nullable();
            $table->string('al_exam_year')->nullable();
            $table->string('al_exam_stream')->nullable();
            $table->string('al_index_no')->nullable();
            $table->string('z_score_value')->nullable();
            $table->text('al_certificate')->nullable();
            $table->json('al_exam_subjects')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_exams');
    }
};
