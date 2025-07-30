<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Semester;

class SemesterRegistrationController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        $semesters = Semester::all();
        
        return view('semester_registration', compact('courses', 'intakes', 'semesters'));
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request
        \Log::info('Semester registration store method called with data:', $request->all());
        
        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester_id' => 'required|exists:semesters,id',
            'location' => 'required|string',
            'specialization' => 'nullable|string|max:255',
            'register_students' => 'required|string', // Changed to string since we're sending JSON
        ]);

        try {
            // Get the selected students
            $selectedStudents = $request->input('register_students', []);
            
            // If register_students is a JSON string, decode it
            if (is_string($selectedStudents)) {
                $selectedStudents = json_decode($selectedStudents, true);
            }
            
            // Validate that we have a valid array after decoding
            if (!is_array($selectedStudents) || empty($selectedStudents)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students selected for registration.'
                ], 400);
            }
            
            // Validate that all student IDs exist
            $validStudentIds = \App\Models\Student::whereIn('student_id', $selectedStudents)->pluck('student_id')->toArray();
            $invalidStudentIds = array_diff($selectedStudents, $validStudentIds);
            
            if (!empty($invalidStudentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected students do not exist in the system.'
                ], 400);
            }
            
            \Log::info('Semester registration request:', [
                'course_id' => $request->course_id,
                'intake_id' => $request->intake_id,
                'semester_id' => $request->semester_id,
                'location' => $request->location,
                'specialization' => $request->specialization,
                'selected_students' => $selectedStudents
            ]);
            
            // Check for existing registrations
            $existingRegistrations = \App\Models\SemesterRegistration::where('semester_id', $request->semester_id)
                ->whereIn('student_id', $selectedStudents)
                ->pluck('student_id')
                ->toArray();
            
            // Filter out students who are already registered
            $newStudents = array_diff($selectedStudents, $existingRegistrations);
            
            if (empty($newStudents)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected students are already registered for this semester.'
                ], 400);
            }
            
            // Prepare registration data for new students only
            $registrationData = [];
            $registrationDate = now()->toDateString();
            
            foreach ($newStudents as $studentId) {
                $registrationData[] = [
                    'student_id' => $studentId,
                    'semester_id' => $request->semester_id,
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'location' => $request->location,
                    'specialization' => $request->specialization,
                    'status' => 'registered',
                    'registration_date' => $registrationDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Save semester registrations
            try {
                \App\Models\SemesterRegistration::insert($registrationData);
                
                $alreadyRegisteredCount = count($existingRegistrations);
                $newlyRegisteredCount = count($newStudents);
                
                $message = "Semester registrations updated successfully! {$newlyRegisteredCount} students registered.";
                if ($alreadyRegisteredCount > 0) {
                    $message .= " ({$alreadyRegisteredCount} students were already registered)";
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            } catch (\Exception $e) {
                \Log::error('Database error during semester registration: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred while saving registrations. Please try again.'
                ], 500);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in semester registration: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving semester registrations: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving registrations. Please try again.'
            ], 500);
        }
    }

    // 1. Get courses by location (degree programs only)
    public function getCoursesByLocation(Request $request) {
        $location = $request->input('location');
        $courses = \App\Models\Course::where('location', $location)
            ->where('course_type', 'degree')
            ->get(['course_id', 'course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    // 2. Get ongoing intakes for a course/location
    public function getOngoingIntakes(Request $request) {
        $courseId = $request->input('course_id');
        $location = $request->input('location');
        $now = now();
        
        \Log::info('getOngoingIntakes called with:', [
            'course_id' => $courseId,
            'location' => $location,
            'current_date' => $now
        ]);
        
        // First, get intakes that are currently active (within date range)
        $activeIntakes = \App\Models\Intake::where('course_name', function($q) use ($courseId) {
                $q->select('course_name')->from('courses')->where('course_id', $courseId)->limit(1);
            })
            ->where('location', $location)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get(['intake_id', 'batch']);
            
        \Log::info('Active intakes found:', ['count' => $activeIntakes->count(), 'intakes' => $activeIntakes->toArray()]);
            
        // Also get intakes that have semesters created for them
        $intakesWithSemesters = \App\Models\Intake::where('course_name', function($q) use ($courseId) {
                $q->select('course_name')->from('courses')->where('course_id', $courseId)->limit(1);
            })
            ->where('location', $location)
            ->whereIn('intake_id', function($q) use ($courseId) {
                $q->select('intake_id')->from('semesters')->where('course_id', $courseId);
            })
            ->get(['intake_id', 'batch']);
            
        \Log::info('Intakes with semesters found:', ['count' => $intakesWithSemesters->count(), 'intakes' => $intakesWithSemesters->toArray()]);
            
        // Merge and deduplicate the results
        $allIntakes = $activeIntakes->merge($intakesWithSemesters)->unique('intake_id');
        
        \Log::info('Final intakes returned:', ['count' => $allIntakes->count(), 'intakes' => $allIntakes->toArray()]);
        
        return response()->json(['success' => true, 'intakes' => $allIntakes]);
    }

    // 3. Get open semesters for a course/intake/location
    public function getOpenSemesters(Request $request) {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        
        \Log::info('getOpenSemesters called with:', [
            'course_id' => $courseId,
            'intake_id' => $intakeId
        ]);
        
        // Get all semesters for this course and intake
        $semesters = \App\Models\Semester::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->get(['id', 'name', 'status'])
            ->map(function($semester) {
                return [
                    'semester_id' => $semester->id,
                    'semester_name' => $semester->name,
                    'status' => $semester->status
                ];
            });
            
        \Log::info('Found semesters:', ['count' => $semesters->count(), 'semesters' => $semesters->toArray()]);
            
        return response()->json(['success' => true, 'semesters' => $semesters]);
    }

    // 4. Get eligible students for a course/intake (registered from eligibility page)
    public function getEligibleStudents(Request $request) {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        $students = \App\Models\CourseRegistration::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with('student')
            ->get()
            ->map(function($reg) {
                return [
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->name_with_initials,
                    'email' => $reg->student->email,
                    'nic' => $reg->student->id_value,
                ];
            });
        return response()->json(['success' => true, 'students' => $students]);
    }

    // 4. Get all possible semesters for a course (for semester creation page)
    public function getAllSemestersForCourse(Request $request) {
        $courseId = $request->input('course_id');
        $course = \App\Models\Course::find($courseId);
        if (!$course || !$course->no_of_semesters) {
            return response()->json(['success' => false, 'semesters' => [], 'message' => 'Course not found or no semesters defined.']);
        }
        $semesters = [];
        for ($i = 1; $i <= $course->no_of_semesters; $i++) {
            $semesters[] = [
                'semester_id' => $i,
                'semester_name' => 'Semester ' . $i
            ];
        }
        return response()->json(['success' => true, 'semesters' => $semesters]);
    }
} 