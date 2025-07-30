<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration {
    public function up(): void {
        // Use raw SQL to modify the enum column, as Doctrine DBAL cannot handle enum changes
        DB::statement("ALTER TABLE modules MODIFY module_type ENUM('core', 'elective', 'special_unit_compulsory') NOT NULL");
    }

    public function down(): void {
        Schema::table('modules', function (Blueprint $table) {
            $table->enum('module_type', ['core', 'elective'])->change(); // fallback for down
        });
    }
}; 