<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('exam_results', function (Blueprint $table) {
            // Change semester column to unsignedBigInteger (integer)
            $table->unsignedBigInteger('semester')->change();
        });
    }

    public function down(): void {
        Schema::table('exam_results', function (Blueprint $table) {
            // Revert back to string if needed
            $table->string('semester', 50)->change();
        });
    }
};