<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking semesters for course_id=3 and intake_id=2:\n\n";

// Get all semesters for this combination
$semesters = \App\Models\Semester::where('course_id', 3)
    ->where('intake_id', 2)
    ->get();

echo "All semesters for course_id=3 and intake_id=2:\n";
foreach ($semesters as $semester) {
    echo "- Semester ID: " . $semester->id . 
         ", Name: " . $semester->name . 
         ", Status: " . $semester->status .
         ", Start Date: " . ($semester->start_date ?? 'NULL') .
         ", End Date: " . ($semester->end_date ?? 'NULL') . "\n";
}

echo "\nSemesters with proper start_date and end_date (likely created through semester creation page):\n";
$properSemesters = \App\Models\Semester::where('course_id', 3)
    ->where('intake_id', 2)
    ->whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->get();

foreach ($properSemesters as $semester) {
    echo "- Semester ID: " . $semester->id . 
         ", Name: " . $semester->name . 
         ", Status: " . $semester->status .
         ", Start Date: " . $semester->start_date .
         ", End Date: " . $semester->end_date . "\n";
}

echo "\nSemesters without proper dates (likely created manually):\n";
$manualSemesters = \App\Models\Semester::where('course_id', 3)
    ->where('intake_id', 2)
    ->where(function($query) {
        $query->whereNull('start_date')
              ->orWhereNull('end_date');
    })
    ->get();

foreach ($manualSemesters as $semester) {
    echo "- Semester ID: " . $semester->id . 
         ", Name: " . $semester->name . 
         ", Status: " . $semester->status .
         ", Start Date: " . ($semester->start_date ?? 'NULL') .
         ", End Date: " . ($semester->end_date ?? 'NULL') . "\n";
} 