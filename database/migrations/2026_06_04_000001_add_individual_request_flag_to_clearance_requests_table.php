<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clearance_requests', function (Blueprint $table) {
            $table->boolean('is_individual_request')->default(false)->after('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clearance_requests', function (Blueprint $table) {
            $table->dropColumn('is_individual_request');
        });
    }
};
