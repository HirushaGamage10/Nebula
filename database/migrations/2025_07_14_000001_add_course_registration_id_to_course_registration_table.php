<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('course_registration', function (Blueprint $table) {
            $table->string('course_registration_id')->nullable()->after('intake_id');
        });
    }

    public function down(): void {
        Schema::table('course_registration', function (Blueprint $table) {
            $table->dropColumn('course_registration_id');
        });
    }
}; 