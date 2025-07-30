<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_lists', function (Blueprint $table) {
            $table->id();
            $table->enum('location', ['Welisara', 'Peradeniya', 'Moratuwa']);
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->foreignId('intake_id')->constrained('intakes', 'intake_id')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->enum('type', ['Permanent', 'Temporary']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_lists');
    }
};
