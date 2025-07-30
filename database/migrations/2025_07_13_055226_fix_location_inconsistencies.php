<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix location inconsistencies by updating any 'Mathara' to 'Moratuwa'
        // and ensuring all tables use consistent location values
        
        // Update students table
        DB::table('students')
            ->where('institute_location', 'Mathara')
            ->update(['institute_location' => 'Moratuwa']);
            
        // Update courses table
        DB::table('courses')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update intakes table
        DB::table('intakes')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update course_registration table
        DB::table('course_registration')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update attendance table
        DB::table('attendance')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update exam_results table
        DB::table('exam_results')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update student_lists table
        DB::table('student_lists')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update module_management table
        DB::table('module_management')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update timetable table
        DB::table('timetable')
            ->where('location', 'Mathara')
            ->update(['location' => 'Moratuwa']);
            
        // Update other_information table if it has location field
        if (Schema::hasColumn('other_information', 'location')) {
            DB::table('other_information')
                ->where('location', 'Mathara')
                ->update(['location' => 'Moratuwa']);
        }
        
        // Update users table if it has location field
        if (Schema::hasColumn('users', 'location')) {
            DB::table('users')
                ->where('location', 'Mathara')
                ->update(['location' => 'Moratuwa']);
        }
        
        // Update clearance_forms table if it has location field
        if (Schema::hasColumn('clearance_forms', 'location')) {
            DB::table('clearance_forms')
                ->where('location', 'Mathara')
                ->update(['location' => 'Moratuwa']);
        }
        
        // Update payment_plans table if it has location field
        if (Schema::hasColumn('payment_plans', 'location')) {
            DB::table('payment_plans')
                ->where('location', 'Mathara')
                ->update(['location' => 'Moratuwa']);
        }
        
        // Update semesters table if it has location field
        if (Schema::hasColumn('semesters', 'location')) {
            DB::table('semesters')
                ->where('location', 'Mathara')
                ->update(['location' => 'Moratuwa']);
        }
        
        // Update semester_registrations table if it has location field
        if (Schema::hasColumn('semester_registrations', 'location')) {
            DB::table('semester_registrations')
                ->where('location', 'Mathara')
                ->update(['location' => 'Moratuwa']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse the changes by updating 'Moratuwa' back to 'Mathara'
        // Note: This is for rollback purposes, but should be used carefully
        
        // Update students table
        DB::table('students')
            ->where('institute_location', 'Moratuwa')
            ->update(['institute_location' => 'Mathara']);
            
        // Update courses table
        DB::table('courses')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update intakes table
        DB::table('intakes')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update course_registration table
        DB::table('course_registration')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update attendance table
        DB::table('attendance')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update exam_results table
        DB::table('exam_results')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update student_lists table
        DB::table('student_lists')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update module_management table
        DB::table('module_management')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update timetable table
        DB::table('timetable')
            ->where('location', 'Moratuwa')
            ->update(['location' => 'Mathara']);
            
        // Update other_information table if it has location field
        if (Schema::hasColumn('other_information', 'location')) {
            DB::table('other_information')
                ->where('location', 'Moratuwa')
                ->update(['location' => 'Mathara']);
        }
        
        // Update users table if it has location field
        if (Schema::hasColumn('users', 'location')) {
            DB::table('users')
                ->where('location', 'Moratuwa')
                ->update(['location' => 'Mathara']);
        }
        
        // Update clearance_forms table if it has location field
        if (Schema::hasColumn('clearance_forms', 'location')) {
            DB::table('clearance_forms')
                ->where('location', 'Moratuwa')
                ->update(['location' => 'Mathara']);
        }
        
        // Update payment_plans table if it has location field
        if (Schema::hasColumn('payment_plans', 'location')) {
            DB::table('payment_plans')
                ->where('location', 'Moratuwa')
                ->update(['location' => 'Mathara']);
        }
        
        // Update semesters table if it has location field
        if (Schema::hasColumn('semesters', 'location')) {
            DB::table('semesters')
                ->where('location', 'Moratuwa')
                ->update(['location' => 'Mathara']);
        }
        
        // Update semester_registrations table if it has location field
        if (Schema::hasColumn('semester_registrations', 'location')) {
            DB::table('semester_registrations')
                ->where('location', 'Moratuwa')
                ->update(['location' => 'Mathara']);
        }
    }
};
