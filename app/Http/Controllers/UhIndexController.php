<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UhIndexController extends Controller
{
    // Show the UH index number page
    public function showPage()
    {
        return view('uh_index_numbers');
    }

    // Fetch courses by location (AJAX)
    public function getCoursesByLocation(Request $request)
    {
        try {
            $location = $request->input('location');
            $courses = Course::where('location', $location)->get(['course_id', 'course_name']);
            return response()->json(['courses' => $courses]);
        } catch (\Exception $e) {
            Log::error('Error fetching courses by location: ' . $e->getMessage());
            return response()->json(['courses' => [], 'error' => 'Failed to fetch courses']);
        }
    }

    // Fetch intakes by course (AJAX)
    public function getIntakesByCourse(Request $request)
    {
        try {
            $courseId = $request->input('course_id');
            // Get the course name first
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['intakes' => []]);
            }
            
            // Get intakes by course name
            $intakes = Intake::where('course_name', $course->course_name)->get(['intake_id', 'batch']);
            return response()->json(['intakes' => $intakes]);
        } catch (\Exception $e) {
            Log::error('Error fetching intakes by course: ' . $e->getMessage());
            return response()->json(['intakes' => [], 'error' => 'Failed to fetch intakes']);
        }
    }

    // Fetch students by intake (AJAX)
    public function getStudentsByIntake(Request $request)
    {
        try {
            $intakeId = $request->input('intake_id');
            $registrations = CourseRegistration::where('intake_id', $intakeId)
                ->with('student')
                ->get();
            $students = $registrations->map(function($reg) {
                return [
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->full_name,
                    'uh_index_number' => $reg->uh_index_number ?? '',
                ];
            });
            return response()->json(['students' => $students]);
        } catch (\Exception $e) {
            Log::error('Error fetching students by intake: ' . $e->getMessage());
            return response()->json(['students' => [], 'error' => 'Failed to fetch students']);
        }
    }

    // Save UH index numbers for students (AJAX)
    public function saveUhIndexNumbers(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'students' => 'required|array',
                'students.*.student_id' => 'required|string',
                'students.*.uh_index_number' => 'nullable|string|max:255',
            ]);

            $data = $request->input('students');
            $updatedCount = 0;
            $errors = [];

            Log::info('Saving external institute student IDs', ['data' => $data]);

            // Use database transaction for data integrity
            DB::beginTransaction();

            try {
                foreach ($data as $item) {
                    $studentId = $item['student_id'];
                    $uhIndexNumber = $item['uh_index_number'] ?? '';

                    // Find the course registration for this student
                    $registration = CourseRegistration::where('student_id', $studentId)->first();
                    
                    if ($registration) {
                        $registration->update(['uh_index_number' => $uhIndexNumber]);
                        $updatedCount++;
                        Log::info("Updated external institute ID for student: {$studentId}", [
                            'student_id' => $studentId,
                            'uh_index_number' => $uhIndexNumber
                        ]);
                    } else {
                        $errors[] = "No course registration found for student ID: {$studentId}";
                        Log::warning("No course registration found for student: {$studentId}");
                    }
                }

                DB::commit();

                if (count($errors) > 0) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Some updates failed: ' . implode(', ', $errors),
                        'updated_count' => $updatedCount,
                        'errors' => $errors
                    ]);
                }

                return response()->json([
                    'success' => true, 
                    'message' => "External institute student IDs saved successfully. Updated {$updatedCount} records.",
                    'updated_count' => $updatedCount
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Database error while saving external institute IDs: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in saveUhIndexNumbers: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Invalid data format provided.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error saving external institute student IDs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving external institute student IDs. Please try again.'
            ], 500);
        }
    }
} 