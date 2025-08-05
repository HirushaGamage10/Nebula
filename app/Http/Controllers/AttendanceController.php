<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;
use App\Models\Student;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AttendanceController extends Controller
{
    public function index()
    {
        $courses = Course::all(['course_id', 'course_name']);
        $intakes = Intake::all(['intake_id', 'batch']);
        
        return view('attendance', compact('courses', 'intakes'));
    }

    public function getCoursesByLocation(Request $request)
    {
        $location = $request->query('location');
        $courseType = $request->query('course_type');

        if (!$location || !$courseType) {
            return response()->json(['success' => false, 'message' => 'Location and Course Type are required.']);
        }
        try {
            $courses = Course::select('course_id', 'course_name')
                ->where('location', $location)
                ->where('course_type', $courseType)
                ->orderBy('course_name', 'asc')
                ->get();

            if ($courses->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No courses found for this location and type.']);
            }

            return response()->json(['success' => true, 'courses' => $courses]);
        } catch (\Exception $e) {
            \Log::error('Error fetching courses by location: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching courses.'], 500);
        }
    }

    public function getIntakesForCourseAndLocation(Request $request, $courseId, $location)
    {
        try {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['error' => 'Course not found.'], 404);
            }

            $intakes = Intake::where('course_name', $course->course_name)
                            ->where('location', $location)
                            ->orderBy('batch')
                            ->get(['intake_id', 'batch']);

            return response()->json(['intakes' => $intakes]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function getSemesters(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
        ]);

        $course = Course::find($request->course_id);
        $intake = Intake::find($request->intake_id);

        if (!$course || !$intake) {
            return response()->json(['error' => 'Invalid course or intake.'], 404);
        }

        $semesters = \App\Models\Semester::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->whereIn('status', ['active', 'upcoming'])
            ->get(['id as semester_id', 'name as semester_name']);
            
        return response()->json(['semesters' => $semesters]);
    }

    public function getFilteredModules(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'semester' => 'required|integer',
            'location' => 'required|string',
        ]);

        $courseId = $request->input('course_id');
        $semesterId = $request->input('semester');

        // Get the semester by ID
        $semester = \App\Models\Semester::where('course_id', $courseId)
            ->where('intake_id', $request->input('intake_id'))
            ->where('id', $semesterId)
            ->first();

        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Filter modules by semester using the semester_module table
        $modules = \App\Models\Module::join('semester_module', 'modules.module_id', '=', 'semester_module.module_id')
            ->where('semester_module.semester_id', $semester->id)
            ->select('modules.module_id', 'modules.module_name')
            ->get();

        return response()->json(['modules' => $modules]);
    }

    public function getStudentsForAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required',
            'module_id' => 'required|exists:modules,module_id',
        ]);

        $courseId = $request->course_id;
        $intakeId = $request->intake_id;
        $location = $request->location;
        $semesterId = $request->semester;
        $moduleId = $request->module_id;

        // Get the semester to determine if it's core or elective
        $semester = \App\Models\Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Check if this is a core module (assigned to semester) or elective module
        $isCoreModule = \DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->where('module_id', $moduleId)
            ->exists();

        if ($isCoreModule) {
            // For core modules: Get students registered for the semester
            $students = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('status', 'registered')
                ->with('student')
                ->get()
                ->map(function($reg) {
                    return [
                        'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                        'student_id' => $reg->student->student_id,
                        'name_with_initials' => $reg->student->name_with_initials,
                    ];
                });
        } else {
            // For elective modules: Get students registered for the specific module
            $students = \App\Models\ModuleManagement::where('module_id', $moduleId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->with('student')
                ->get()
                ->map(function($reg) {
                    return [
                        'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                        'student_id' => $reg->student->student_id,
                        'name_with_initials' => $reg->student->name_with_initials,
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'semester' => 'required|string',
            'module_id' => 'required|integer',
            'date' => 'required|date',
            'attendance_data' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $date = Carbon::parse($request->date);
            
            // Delete existing attendance records for this date, course, intake, semester, and module
            Attendance::where('date', $date)
                     ->where('course_id', $request->course_id)
                     ->where('intake_id', $request->intake_id)
                     ->where('semester', $request->semester)
                     ->where('module_id', $request->module_id)
                     ->delete();

            // Insert new attendance records
            $attendanceRecords = [];
            foreach ($request->attendance_data as $studentData) {
                $attendanceRecords[] = [
                    'location' => $request->location,
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'semester' => $request->semester,
                    'module_id' => $request->module_id,
                    'date' => $date,
                    'student_id' => $studentData['student_id'],
                    'status' => $studentData['status'] ?? false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            Attendance::insert($attendanceRecords);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendanceHistory(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'semester' => 'required|string',
            'module_id' => 'required|integer',
            'date' => 'required|date'
        ]);

        $attendance = Attendance::where('location', $request->location)
                               ->where('course_id', $request->course_id)
                               ->where('intake_id', $request->intake_id)
                               ->where('semester', $request->semester)
                               ->where('module_id', $request->module_id)
                               ->where('date', $request->date)
                               ->with('student')
                               ->get();

        return response()->json([
            'success' => true,
            'attendance' => $attendance
        ]);
    }

    // Debug method to check database data
    public function debugData()
    {
        $courses = Course::all(['course_id', 'course_name', 'location']);
        $intakes = Intake::all(['intake_id', 'course_name', 'location', 'batch']);
        $courseTypes = Course::select('course_type')->distinct()->get();
        
        return response()->json([
            'distinct_course_types' => $courseTypes,
            'courses' => $courses,
            'intakes' => $intakes,
            'message' => 'Check the browser console for detailed data'
        ]);
    }

    public function getOverallAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required',
            'module_id' => 'required|exists:modules,module_id',
        ]);

        $courseId = $request->course_id;
        $intakeId = $request->intake_id;
        $location = $request->location;
        $semesterId = $request->semester;
        $moduleId = $request->module_id;

        // Get the semester to determine if it's core or elective
        $semester = \App\Models\Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Check if this is a core module (assigned to semester) or elective module
        $isCoreModule = \DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->where('module_id', $moduleId)
            ->exists();

        // Get all attendance sessions for this filter (by module)
        $attendanceSessions = \App\Models\Attendance::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->where('location', $location)
            ->where('semester', $semester->name)
            ->where('module_id', $moduleId)
            ->select('date')
            ->distinct()
            ->get();
        $totalSessions = $attendanceSessions->count();

        if ($isCoreModule) {
            // For core modules: Get students registered for the semester
            $registrations = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('status', 'registered')
                ->with('student')
                ->get();
        } else {
            // For elective modules: Get students registered for the specific module
            $registrations = \App\Models\ModuleManagement::where('module_id', $moduleId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->with('student')
                ->get();
        }

        $attendanceData = [];
        foreach ($registrations as $reg) {
            $attendedSessions = \App\Models\Attendance::where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->where('module_id', $moduleId)
                ->where('student_id', $reg->student_id)
                ->where('status', true)
                ->count();
            $attendanceData[] = [
                'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                'name_with_initials' => $reg->student->name_with_initials,
                'total_sessions' => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'percentage' => $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) : 0
            ];
        }
        return response()->json([
            'success' => true,
            'attendance' => $attendanceData
        ]);
    }
} 