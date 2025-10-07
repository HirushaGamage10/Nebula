<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Step 1: Change ENUM definition
        DB::statement("
            ALTER TABLE courses 
            MODIFY COLUMN location ENUM('Welisara', 'Moratuwa', 'Peradeniya') 
            CHARACTER SET utf8mb4 
            COLLATE utf8mb4_unicode_ci 
            NOT NULL
        ");

        // ✅ Step 2 (Optional): Update old records
        DB::statement("UPDATE courses SET location = 'Moratuwa' WHERE location = 'Mathara'");
    }

    public function down(): void
    {
        // Rollback to original enum definition
        DB::statement("
            ALTER TABLE courses 
            MODIFY COLUMN location ENUM('Welisara', 'Mathara', 'Peradeniya') 
            CHARACTER SET utf8mb4 
            COLLATE utf8mb4_unicode_ci 
            NOT NULL
        ");
    }
};

