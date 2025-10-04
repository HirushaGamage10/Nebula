<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Detect DB driver (works for MySQL)
        if (Schema::hasColumn('student_payment_plans', 'status')) {
            DB::statement("ALTER TABLE student_payment_plans MODIFY COLUMN status ENUM('active', 'inactive', 'archived') DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum without 'archived'
        if (Schema::hasColumn('student_payment_plans', 'status')) {
            DB::statement("ALTER TABLE student_payment_plans MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
        }
    }
};
