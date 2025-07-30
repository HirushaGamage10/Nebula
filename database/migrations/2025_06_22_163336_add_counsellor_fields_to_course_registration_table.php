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
        Schema::table('course_registration', function (Blueprint $table) {
            $table->string('counselor_name')->nullable()->after('employee_service_number');
            $table->string('counselor_id')->nullable()->after('counselor_name');
            $table->string('counselor_phone')->nullable()->after('counselor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('course_registration', function (Blueprint $table) {
            $table->dropColumn(['counselor_name', 'counselor_id', 'counselor_phone']);
        });
    }
};
