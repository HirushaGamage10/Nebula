<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['foundation_program', 'btec_completed']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('foundation_program')->default(false)->after('institute_location');
            $table->boolean('btec_completed')->default(false)->after('foundation_program');
        });
    }
};
