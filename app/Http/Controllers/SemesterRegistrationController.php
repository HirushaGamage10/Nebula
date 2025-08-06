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
        \Log::info('Semester registration store method called with data:', $request->all());

        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester_id' => 'required|exists:semesters,id',
            'location' => 'required|string',
            'specialization' => 'nullable|string|max:255',
            'register_students' => 'required|string',
        ]);

        try {
            $selectedStudentsRaw = $request->input('register_students');
            $selectedStudents = json_decode($selectedStudentsRaw, true);

            if (!is_array($selectedStudents) || empty($selectedStudents)) {
                return response()->json(['success' => false, 'message' => 'No students selected for registration.'], 400);
            }

            // Validate structure of each entry (must have student_id and status)
            foreach ($selectedStudents as $entry) {
                if (!isset($entry['student_id']) || !isset($entry['status'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid student entry format.'
                    ], 400);
                }
            }

            // Extract only the IDs for validation
            $studentIds = array_column($selectedStudents, 'student_id');

            // Validate students exist
            $validStudentIds = \App\Models\Student::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
            $invalidStudentIds = array_diff($studentIds, $validStudentIds);

            if (!empty($invalidStudentIds)) {
                return response()->json(['success' => false, 'message' => 'Some selected students do not exist in the system.'], 400);
            }

            // Block if terminated students are being registered again
            $terminatedStudentIds = array_column(array_filter($selectedStudents, function ($entry) {
                return $entry['status'] === 'registered';
            }), 'student_id');

            if (!empty($terminatedStudentIds)) {
                $terminatedRecords = \App\Models\SemesterRegistration::whereIn('student_id', $terminatedStudentIds)
                    ->where('intake_id', $request->intake_id)
                    ->where('status', 'terminated')
                    ->pluck('student_id')
                    ->toArray();

                if (!empty($terminatedRecords)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some selected students are terminated and cannot be registered again.',
                    ], 400);
                }
            }

            // ✅ Perform registration or termination
            foreach ($selectedStudents as $entry) {
                $studentId = $entry['student_id'];
                $status = $entry['status'];

                \App\Models\SemesterRegistration::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'intake_id' => $request->intake_id,
                        'semester_id' => $request->semester_id,
                    ],
                    [
                        'course_id' => $request->course_id,
                        'location' => $request->location,
                        'specialization' => $request->specialization,
                        'status' => $status,
                        'registration_date' => now()->toDateString(),
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Student registration statuses updated successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving semester registrations: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error occurred.'], 500);
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
                $semReg = \App\Models\SemesterRegistration::where('student_id', $reg->student->student_id)
                    ->where('intake_id', $reg->intake_id)
                    ->latest()
                    ->first();

                return [
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->name_with_initials,
                    'email' => $reg->student->email,
                    'nic' => $reg->student->id_value,
                    'status' => $semReg?->status ?? 'pending',
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

        // Get the semesters that have already been created for this course
        $createdSemesterNames = \App\Models\Semester::where('course_id', $courseId)
            ->pluck('name')
            ->toArray();

        // Generate all possible semesters for this course (1 to no_of_semesters)
        $allPossibleSemesters = [];
        for ($i = 1; $i <= $course->no_of_semesters; $i++) {
            // Only include semesters that haven't been created yet
            if (!in_array($i, $createdSemesterNames)) {
                $allPossibleSemesters[] = [
                    'semester_id' => $i, // Use the semester number as ID for creation
                    'semester_name' => 'Semester ' . $i
                ];
            }
        }

        return response()->json(['success' => true, 'semesters' => $allPossibleSemesters]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'status' => 'required|in:registered,terminated',
        ]);

        $reg = \App\Models\SemesterRegistration::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'semester_id' => $request->semester_id,
            ],
            [
                'intake_id' => $request->intake_id,
                'status' => $request->status,
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

}
