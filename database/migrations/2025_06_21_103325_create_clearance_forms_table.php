<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clearances_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->boolean('library_clear')->default(0);
            $table->boolean('hostel_clear')->default(0);
            $table->boolean('bursary_clear')->default(0);
            $table->boolean('project_clear')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clearances_forms');
    }
};
