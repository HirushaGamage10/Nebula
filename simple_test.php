<?php

// Simple test to check basic import functionality
$csvContent = 'Student Name,Course Name,Module Name,Intake,Location,Semester,Marks,Grade,Remarks
Metheesha A,B.eng. Electrical & Electronic Engineering-uh,Programming,2025-August,Welisara,1,85,B,Good
Sithumini Priyanwada,B.eng. Electrical & Electronic Engineering-uh,Programming,2025-August,Welisara,1,95,A,Very Good';

// Simulate CSV parsing
$lines = explode("\n", $csvContent);
$headers = str_getcsv($lines[0]);

echo "Headers: " . json_encode($headers) . "\n";

for ($i = 1; $i < count($lines); $i++) {
    if (trim($lines[$i]) === '') continue;
    
    $row = str_getcsv($lines[$i]);
    $rowData = array_combine($headers, $row);
    
    echo "Row $i: " . json_encode($rowData) . "\n";
    
    // Extract basic data
    $studentName = trim($rowData['Student Name'] ?? '');
    $courseName = trim($rowData['Course Name'] ?? '');
    $moduleName = trim($rowData['Module Name'] ?? '');
    $marks = trim($rowData['Marks'] ?? '');
    
    echo "Extracted - Student: $studentName, Course: $courseName, Module: $moduleName, Marks: $marks\n";
}

?>