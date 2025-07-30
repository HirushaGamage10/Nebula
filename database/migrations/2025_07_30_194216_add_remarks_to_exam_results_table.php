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
        Schema::table('exam_results', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
