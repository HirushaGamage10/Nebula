<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get current ENUM values
        $currentEnum = DB::select(DB::raw("SHOW COLUMNS FROM course_registration WHERE Field = 'status'"))[0]->Type;

        // Extract ENUM values
        preg_match('/enum\((.*)\)/', $currentEnum, $matches);
        $values = str_getcsv($matches[1], ',', "'");

        // Add new value if not exists
        if (!in_array('completed', $values)) {
            $values[] = 'completed';
        }

        $enumList = "'" . implode("','", $values) . "'";

        // Alter column
        DB::statement("ALTER TABLE course_registration MODIFY status ENUM($enumList) NOT NULL");
    }

    public function down(): void
    {
        // Rollback - remove 'completed' if needed
        $currentEnum = DB::select(DB::raw("SHOW COLUMNS FROM course_registration WHERE Field = 'status'"))[0]->Type;
        preg_match('/enum\((.*)\)/', $currentEnum, $matches);
        $values = str_getcsv($matches[1], ',', "'");

        // Remove the 'completed' value
        $values = array_filter($values, fn($v) => $v !== 'completed');
        $enumList = "'" . implode("','", $values) . "'";

        DB::statement("ALTER TABLE course_registration MODIFY status ENUM($enumList) NOT NULL");
    }
};
