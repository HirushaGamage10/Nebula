<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->enum('location', ['Welisara', 'Mathara', 'Peradeniya']);
            $table->string('course_name');
            $table->integer('no_of_semesters');
            $table->string('duration');
            $table->integer('semester'); // If needed to mark current semester
            $table->string('modules')->nullable(); // Optional; normally normalized in course_modules
            $table->string('training_period')->nullable();
            $table->integer('min_credits');
            $table->text('entry_qualification')->nullable();
            $table->boolean('conducted_by')->default(0); // Could be 1 for SLT, 0 for external
            $table->enum('course_medium', ['Sinhala', 'English']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('courses');
    }
};

