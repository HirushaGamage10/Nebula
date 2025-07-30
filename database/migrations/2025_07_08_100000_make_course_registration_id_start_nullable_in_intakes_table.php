<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('intakes', function (Blueprint $table) {
            // $table->string('course_registration_id_start')->nullable()->change();
            // Commented out because the column does not exist and was causing migration errors.
        });
    }

    public function down(): void {
        Schema::table('intakes', function (Blueprint $table) {
            // $table->string('course_registration_id_start')->nullable(false)->change();
            // Commented out because the column does not exist and was causing migration errors.
        });
    }
}; 