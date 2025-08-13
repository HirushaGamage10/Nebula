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
        Schema::table('intakes', function (Blueprint $table) {
            // Check if franchise_payment column exists before modifying it
            if (Schema::hasColumn('intakes', 'franchise_payment')) {
                $table->string('franchise_payment')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Check if franchise_payment column exists before modifying it
            if (Schema::hasColumn('intakes', 'franchise_payment')) {
                $table->string('franchise_payment')->nullable(false)->change();
            }
        });
    }
}; 