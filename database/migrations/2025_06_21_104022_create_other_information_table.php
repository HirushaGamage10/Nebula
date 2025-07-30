<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('other_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');

            $table->text('disciplinary_issues')->nullable();
            $table->string('disciplinary_issue_document')->nullable();

            // Higher studies section
            $table->boolean('continue_higher_studies')->default(0);
            $table->string('institute')->nullable();
            $table->string('field_of_study')->nullable();

            // Employment section
            $table->boolean('currently_employee')->default(0);
            $table->string('job_title')->nullable();
            $table->string('workplace')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('other_information');
    }
};
