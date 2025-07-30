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
        Schema::table('other_information', function (Blueprint $table) {
            $table->text('other_information')->nullable()->after('workplace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_information', function (Blueprint $table) {
            $table->dropColumn('other_information');
        });
    }
};
