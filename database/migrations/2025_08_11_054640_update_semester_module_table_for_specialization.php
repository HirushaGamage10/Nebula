<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('semester_module', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['module_id']);
        });

        Schema::table('semester_module', function (Blueprint $table) {
            // Add specialization column if not exists
            if (!Schema::hasColumn('semester_module', 'specialization')) {
                $table->string('specialization')->nullable()->after('module_id');
            }

            // Drop old primary key
            $table->dropPrimary(['semester_id', 'module_id']);

            // Add new composite primary key
            $table->primary(['semester_id', 'module_id', 'specialization']);
        });

        Schema::table('semester_module', function (Blueprint $table) {
            // Re-add foreign keys
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('semester_module', function (Blueprint $table) {
            $table->dropPrimary(['semester_id', 'module_id', 'specialization']);
            $table->dropColumn('specialization');
            $table->primary(['semester_id', 'module_id']);
        });
    }
};