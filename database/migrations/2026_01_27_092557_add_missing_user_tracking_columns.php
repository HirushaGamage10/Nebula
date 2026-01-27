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
        // Add tracking columns to course_registration (singular, not plural)
        if (Schema::hasTable('course_registration')) {
            Schema::table('course_registration', function (Blueprint $table) {
                if (!Schema::hasColumn('course_registration', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
                }
                
                if (!Schema::hasColumn('course_registration', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                    $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
                }
                
                if (Schema::hasColumn('course_registration', 'deleted_at') && 
                    !Schema::hasColumn('course_registration', 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable();
                    $table->foreign('deleted_by')->references('user_id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('course_registration')) {
            Schema::table('course_registration', function (Blueprint $table) {
                if (Schema::hasColumn('course_registration', 'created_by')) {
                    $table->dropForeign(['created_by']);
                    $table->dropColumn('created_by');
                }
                
                if (Schema::hasColumn('course_registration', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn('updated_by');
                }
                
                if (Schema::hasColumn('course_registration', 'deleted_by')) {
                    $table->dropForeign(['deleted_by']);
                    $table->dropColumn('deleted_by');
                }
            });
        }
    }
};
