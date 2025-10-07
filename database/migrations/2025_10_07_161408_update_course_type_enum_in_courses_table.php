<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the ENUM column to include 'diploma'
        DB::statement("ALTER TABLE courses MODIFY course_type ENUM('degree', 'diploma', 'certificate') NOT NULL DEFAULT 'degree'");
    }

    public function down(): void
    {
        // Revert to the old ENUM without 'diploma'
        DB::statement("ALTER TABLE courses MODIFY course_type ENUM('degree', 'certificate') NOT NULL DEFAULT 'degree'");
    }
};
