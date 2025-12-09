<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\Intake;

class StudentViewController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('course_name')->get();
        $intakes = Intake::orderBy('batch')->get();
        return view('student_view', compact('courses', 'intakes'));
    }

    public function filter(Request $request)
    {
        $query = Student::query()
            ->with(['courseRegistrations.course', 'courseRegistrations.intake']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id)
                  ->orWhere('id_value', $request->student_id);
        }

        if ($request->filled('course_id')) {
            $query->whereHas('courseRegistrations', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('intake_id')) {
            $query->whereHas('courseRegistrations', function($q) use ($request) {
                $q->where('intake_id', $request->intake_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('academic_status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('institute_location', $request->location);
        }

        $students = $query->orderBy('student_id', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function getStudentCourses(Request $request)
    {
        $studentId = $request->query('student_id');
        
        $student = Student::where('student_id', $studentId)
                    ->orWhere('id_value', $studentId)
                    ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'courses' => [],
                'message' => 'Student not found'
            ]);
        }

        $courses = $student->courseRegistrations()
                    ->with('course')
                    ->get()
                    ->pluck('course')
                    ->unique('course_id')
                    ->values();

        return response()->json([
            'success' => true,
            'courses' => $courses,
        ]);
    }

}
