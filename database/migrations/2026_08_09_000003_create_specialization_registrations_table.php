<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('specialization_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('intake_id');
            $table->string('location');
            $table->string('specialization');
            $table->enum('status', ['registered', 'cancelled'])->default('registered');
            $table->timestamps();
            $table->unique(['student_id', 'course_id', 'intake_id'], 'specialization_registration_student_cohort_unique');
            $table->index(['course_id', 'intake_id', 'location', 'specialization', 'status'], 'specialization_registration_lookup');
            $table->foreign('student_id')->references('student_id')->on('students')->cascadeOnDelete();
            $table->foreign('course_id')->references('course_id')->on('courses')->cascadeOnDelete();
            $table->foreign('intake_id')->references('intake_id')->on('intakes')->cascadeOnDelete();
        });

        // Preserve the valid assignments already saved by the old semester flow.
        DB::table('semester_registrations')
            ->whereNotNull('specialization')
            ->where('specialization', '<>', '')
            ->orderBy('id')
            ->get(['student_id', 'course_id', 'intake_id', 'location', 'specialization', 'status', 'created_at', 'updated_at'])
            ->each(function ($row) {
                DB::table('specialization_registrations')->updateOrInsert(
                    ['student_id' => $row->student_id, 'course_id' => $row->course_id, 'intake_id' => $row->intake_id],
                    [
                        'location' => $row->location,
                        'specialization' => $row->specialization,
                        'status' => $row->status === 'registered' ? 'registered' : 'cancelled',
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ]
                );
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialization_registrations');
    }
};
