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
        // List of all tables to add user tracking columns
        $tables = [
            'students',
            'courses',
            'modules',
            'intakes',
            'guardian_details',
            'student_exams',
            'users',
            'course_registrations',
            'clearance_forms',
            'exam_results',
            'other_information',
            'student_lists',
            'attendance',
            'module_management',
            'timetable',
            'semesters',
            'semester_registrations',
            'payment_plans',
            'clearance_requests',
            'discounts',
            'payment_installments',
            'student_payment_plans',
            'payment_details',
            'student_status_histories',
            'custom_payments',
            'course_badges',
            'bulk_student_uploads',
            'bulk_revenue_uploads',
            'phases',
            'teams',
            'team_roles',
            'sessions',
            'course_change_logs',
            'course_change_payments',
            'intake_modules',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    // Add created_by column if it doesn't exist
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->unsignedBigInteger('created_by')->nullable();
                        $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
                    }
                    
                    // Add updated_by column if it doesn't exist
                    if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->unsignedBigInteger('updated_by')->nullable();
                        $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
                    }
                    
                    // Add deleted_by column if table has soft deletes (deleted_at column)
                    if (Schema::hasColumn($table->getTable(), 'deleted_at') && 
                        !Schema::hasColumn($table->getTable(), 'deleted_by')) {
                        $table->unsignedBigInteger('deleted_by')->nullable();
                        $table->foreign('deleted_by')->references('user_id')->on('users')->onDelete('set null');
                    }
                });
            }
        }

        // Handle pivot tables separately (they might not have id column)
        $pivotTables = [
            'course_module',
            'semester_module',
            'payment_plan_discounts',
        ];

        foreach ($pivotTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->unsignedBigInteger('created_by')->nullable();
                        $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
                    }
                    
                    if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->unsignedBigInteger('updated_by')->nullable();
                        $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'students',
            'courses',
            'modules',
            'intakes',
            'guardian_details',
            'student_exams',
            'users',
            'course_registrations',
            'clearance_forms',
            'exam_results',
            'other_information',
            'student_lists',
            'attendance',
            'module_management',
            'timetable',
            'semesters',
            'semester_registrations',
            'payment_plans',
            'clearance_requests',
            'discounts',
            'payment_installments',
            'student_payment_plans',
            'payment_details',
            'student_status_histories',
            'custom_payments',
            'course_badges',
            'bulk_student_uploads',
            'bulk_revenue_uploads',
            'phases',
            'teams',
            'team_roles',
            'sessions',
            'course_change_logs',
            'course_change_payments',
            'intake_modules',
            'course_module',
            'semester_module',
            'payment_plan_discounts',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->dropForeign(['created_by']);
                        $table->dropColumn('created_by');
                    }
                    
                    if (Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->dropForeign(['updated_by']);
                        $table->dropColumn('updated_by');
                    }
                    
                    if (Schema::hasColumn($table->getTable(), 'deleted_by')) {
                        $table->dropForeign(['deleted_by']);
                        $table->dropColumn('deleted_by');
                    }
                });
            }
        }
    }
};
