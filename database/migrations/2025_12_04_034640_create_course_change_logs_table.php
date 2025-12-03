<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_change_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('old_intake_id');
            $table->integer('new_intake_id');
            $table->integer('changed_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_change_logs');
    }
};
