<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Semester;
use App\Models\Course;
use App\Models\Intake;

echo "=== Semester Database Check ===\n\n";

// Check if semesters table exists and has data
try {
    $semesterCount = Semester::count();
    echo "Total semesters in database: {$semesterCount}\n\n";
    
    if ($semesterCount > 0) {
        echo "Sample semesters:\n";
        $semesters = Semester::take(5)->get();
        foreach ($semesters as $semester) {
            echo "- ID: {$semester->id}, Name: {$semester->name}, Course ID: {$semester->course_id}, Intake ID: {$semester->intake_id}, Status: {$semester->status}\n";
        }
        echo "\n";
    }
    
    // Check courses
    $courseCount = Course::count();
    echo "Total courses in database: {$courseCount}\n";
    
    if ($courseCount > 0) {
        echo "Sample courses:\n";
        $courses = Course::take(3)->get();
        foreach ($courses as $course) {
            echo "- ID: {$course->course_id}, Name: {$course->course_name}, Location: {$course->location}\n";
        }
        echo "\n";
    }
    
    // Check intakes
    $intakeCount = Intake::count();
    echo "Total intakes in database: {$intakeCount}\n";
    
    if ($intakeCount > 0) {
        echo "Sample intakes:\n";
        $intakes = Intake::take(3)->get();
        foreach ($intakes as $intake) {
            echo "- ID: {$intake->intake_id}, Batch: {$intake->batch}, Course: {$intake->course_name}\n";
        }
        echo "\n";
    }
    
    // Test the specific query that's failing
    echo "=== Testing Semester Query ===\n";
    
    // Get a sample course and intake
    $sampleCourse = Course::first();
    $sampleIntake = Intake::first();
    
    if ($sampleCourse && $sampleIntake) {
        echo "Testing query for Course ID: {$sampleCourse->course_id}, Intake ID: {$sampleIntake->intake_id}\n";
        
        $semesters = Semester::where('course_id', $sampleCourse->course_id)
            ->where('intake_id', $sampleIntake->intake_id)
            ->get(['id', 'name', 'status']);
            
        echo "Found " . $semesters->count() . " semesters for this combination\n";
        
        foreach ($semesters as $semester) {
            echo "- Semester ID: {$semester->id}, Name: {$semester->name}, Status: {$semester->status}\n";
        }
    }
    
    // Check all course-intake combinations that have semesters
    echo "\n=== All Course-Intake Combinations with Semesters ===\n";
    $semesterGroups = Semester::select('course_id', 'intake_id')
        ->groupBy('course_id', 'intake_id')
        ->get();
        
    foreach ($semesterGroups as $group) {
        $course = Course::find($group->course_id);
        $intake = Intake::find($group->intake_id);
        $semesterCount = Semester::where('course_id', $group->course_id)
            ->where('intake_id', $group->intake_id)
            ->count();
            
        echo "Course ID: {$group->course_id} ({$course->course_name}), Intake ID: {$group->intake_id} ({$intake->batch}) - {$semesterCount} semesters\n";
    }
    
    // Check intake dates
    echo "\n=== Intake Date Check ===\n";
    $intakes = Intake::all();
    $now = now();
    echo "Current date: {$now}\n\n";
    
    foreach ($intakes as $intake) {
        $isActive = ($intake->start_date <= $now && $intake->end_date >= $now) ? 'ACTIVE' : 'NOT ACTIVE';
        echo "Intake ID: {$intake->intake_id}, Batch: {$intake->batch}, Start: {$intake->start_date}, End: {$intake->end_date} - {$isActive}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== End of Check ===\n"; 