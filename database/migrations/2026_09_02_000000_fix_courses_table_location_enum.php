<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change the courses table location enum from 'Mathara' to 'Moratuwa'
        Schema::table('courses', function (Blueprint $table) {
            // For MySQL, we need to change the enum definition
            $table->enum('location', ['Welisara', 'Moratuwa', 'Peradeniya'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum definition
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('location', ['Welisara', 'Mathara', 'Peradeniya'])->change();
        });
    }
};
