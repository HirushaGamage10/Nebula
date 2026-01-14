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
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['module_id']);
            
            // Make columns nullable
            $table->unsignedBigInteger('module_id')->nullable()->change();
            $table->string('semester')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['module_id']);
            
            // Make columns non-nullable again
            $table->unsignedBigInteger('module_id')->nullable(false)->change();
            $table->enum('semester', ['1', '2', '3', '4', '5', '6'])->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }
};
