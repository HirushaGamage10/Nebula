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
        $semester = $request->input('semester');

        // Get modules from semester_module table for the specific semester
        $modules = \DB::table('modules')
            ->join('semester_module', 'modules.module_id', '=', 'semester_module.module_id')
            ->join('semesters', 'semester_module.semester_id', '=', 'semesters.id')
            ->where('semesters.course_id', $courseId)
            ->where('semesters.name', $semester)
            ->select('modules.module_id', 'modules.module_name')
            ->distinct()
            ->get();
            
        // If no modules are assigned to this semester, return all modules
        if ($modules->isEmpty()) {
            $modules = \DB::table('modules')
                ->select('module_id', 'module_name')
                ->orderBy('module_name')
                ->get();
        }

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

        // Fetch students registered for the course, intake, location, and semester
        $students = CourseRegistration::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with('student')
            ->get()
            ->map(function($reg) {
                return [
                    'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                    'student_id' => $reg->student->student_id,
                    'name_with_initials' => $reg->student->name_with_initials,
                ];
            });

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

        // Get all students registered for this course/intake/location
        $registrations = \App\Models\CourseRegistration::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->with('student')
            ->get();

        // Get all attendance sessions for this filter (by module)
        $attendanceSessions = \App\Models\Attendance::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where('semester', $request->semester)
            ->where('module_id', $request->module_id)
            ->select('date')
            ->distinct()
            ->get();
        $totalSessions = $attendanceSessions->count();

        $attendanceData = [];
        foreach ($registrations as $reg) {
            $attendedSessions = \App\Models\Attendance::where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->where('location', $request->location)
                ->where('semester', $request->semester)
                ->where('module_id', $request->module_id)
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