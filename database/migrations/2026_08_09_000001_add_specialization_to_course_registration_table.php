<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('course_registration', 'specialization')) {
            Schema::table('course_registration', function (Blueprint $table) {
                $table->string('specialization')->nullable()->after('location');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('course_registration', 'specialization')) {
            Schema::table('course_registration', function (Blueprint $table) {
                $table->dropColumn('specialization');
            });
        }
    }
};
