<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semester_module', function (Blueprint $table) {
            // 1. Drop foreign keys first
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['module_id']);
        });

        // 2. Drop old primary key safely
        DB::statement('ALTER TABLE semester_module DROP PRIMARY KEY');

        Schema::table('semester_module', function (Blueprint $table) {
            // 3. Make specialization nullable
            $table->string('specialization', 255)->nullable()->change();

            // 4. Add new composite PK
            $table->primary(['semester_id', 'module_id']);

            // 5. Re-add foreign keys
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('semester_module', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['module_id']);
        });

        DB::statement('ALTER TABLE semester_module DROP PRIMARY KEY');

        Schema::table('semester_module', function (Blueprint $table) {
            $table->string('specialization', 255)->nullable(false)->change();
            $table->primary(['semester_id', 'module_id', 'specialization']);

            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }
};
