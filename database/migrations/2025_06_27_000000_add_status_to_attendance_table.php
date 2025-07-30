<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('attendance', function (Blueprint $table) {
            $table->boolean('status')->default(false)->after('student_id');
        });
    }

    public function down(): void {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 