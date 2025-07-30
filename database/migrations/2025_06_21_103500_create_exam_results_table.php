<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules', 'module_id')->onDelete('cascade');
            $table->foreignId('intake_id')->constrained('intakes', 'intake_id')->onDelete('cascade');

            $table->enum('location', ['Welisara', 'Moratuwa', 'Peradeniya']);
            $table->enum('semester', ['1','2','3','4','5','6']);

            $table->integer('marks');
            $table->string('grade', 5);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('exam_results');
    }
};
