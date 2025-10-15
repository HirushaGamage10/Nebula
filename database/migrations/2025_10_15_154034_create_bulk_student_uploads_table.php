<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bulk_student_uploads', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->string('location');
            $table->string('course')->nullable();
            $table->integer('student_count');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bulk_student_uploads');
    }
};