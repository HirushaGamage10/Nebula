<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // A later semester may be pending while the student's degree/diploma
        // specialization is still valid. Restore every legacy registered
        // assignment as an active specialization registration.
        DB::table('semester_registrations')
            ->where('status', 'registered')
            ->whereNotNull('specialization')
            ->where('specialization', '<>', '')
            ->orderBy('id')
            ->get(['student_id', 'course_id', 'intake_id', 'location', 'specialization', 'created_at', 'updated_at'])
            ->each(function ($row) {
                DB::table('specialization_registrations')->updateOrInsert(
                    ['student_id' => $row->student_id, 'course_id' => $row->course_id, 'intake_id' => $row->intake_id],
                    [
                        'location' => $row->location,
                        'specialization' => $row->specialization,
                        'status' => 'registered',
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ]
                );
            });
    }

    public function down(): void
    {
        // This is a data repair migration; the original table remains intact.
    }
};
