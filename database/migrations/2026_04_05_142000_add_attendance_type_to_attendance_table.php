<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('attendance', 'attendance_type')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->string('attendance_type', 50)->default('lectures')->after('date');
                $table->index(['course_id', 'intake_id', 'date', 'attendance_type'], 'attendance_type_lookup_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance', 'attendance_type')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropIndex('attendance_type_lookup_idx');
                $table->dropColumn('attendance_type');
            });
        }
    }
};
