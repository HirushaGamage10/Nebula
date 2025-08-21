<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_status_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->text('reason')->nullable();
            $table->string('document')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->cascadeOnDelete();

            // ⬇️ change 'id' to your actual users PK, e.g. 'user_id'
            $table->foreign('changed_by')
                ->references('user_id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_status_histories');
    }
};
