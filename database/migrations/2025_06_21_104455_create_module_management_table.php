<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('module_management', function (Blueprint $table) {
            $table->id();
            $table->enum('location', ['Welisara', 'Moratuwa', 'Peradeniya']);
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->foreignId('intake_id')->constrained('intakes', 'intake_id')->onDelete('cascade');
            $table->enum('semester', ['1', '2', '3', '4', '5', '6']);
            $table->foreignId('module_id')->constrained('modules', 'module_id')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('module_management');
    }
};
