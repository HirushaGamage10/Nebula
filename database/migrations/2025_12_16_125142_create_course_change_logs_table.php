<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_change_logs')) {
            Schema::create('course_change_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('old_intake_id');
                $table->unsignedBigInteger('old_course_id');
                $table->unsignedBigInteger('new_intake_id');
                $table->unsignedBigInteger('new_course_id');
                $table->unsignedBigInteger('old_payment_plan_id')->nullable();
                $table->decimal('total_paid_amount', 12, 2)->default(0);
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->string('changed_by_name')->nullable();
                $table->timestamp('changed_at')->useCurrent();
                $table->text('remarks')->nullable();
                $table->timestamps();

                // Indexes
                $table->index(['student_id', 'changed_at']);
                $table->index('changed_by');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_change_logs');
    }
};