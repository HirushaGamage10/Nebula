<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // $table->string('course_registration_id_start')->nullable();
            // Commented out because the column does not exist and was causing migration errors.
            // $table->string('course_registration_id_pattern')->nullable()->after('course_registration_id_start')->change();
            // Commented out because the column does not exist and was causing migration errors.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // $table->dropColumn('course_registration_id_start');
            // Commented out because the column does not exist and was causing migration errors.
            // $table->dropColumn('course_registration_id_pattern');
            // Commented out because the column does not exist and was causing migration errors.
        });
    }
};
