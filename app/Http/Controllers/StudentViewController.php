<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\Intake;

class StudentViewController extends Controller
{
    private function normalizeSpecializationValue($value)
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function courseHasSpecializations(?Course $course): bool
    {
        if (!$course || empty($course->specializations)) {
            return false;
        }

        $specializations = is_array($course->specializations)
            ? $course->specializations
            : json_decode($course->specializations, true);

        return is_array($specializations) && count(array_filter($specializations)) > 0;
    }

    public function index()
    {
        $courses = Course::orderBy('course_name')->get();
        $intakes = Intake::orderBy('batch')->get();
        return view('student_management.student_view', compact('courses', 'intakes'));
    }

    public function filter(Request $request)
    {
        $query = Student::query()
            ->with(['courseRegistrations.course', 'courseRegistrations.intake']);

        $selectedCourseId = $request->input('course_id');
        $specialization = $this->normalizeSpecializationValue($request->input('specialization'));

        if ($selectedCourseId) {
            $course = Course::find($selectedCourseId);
            if ($this->courseHasSpecializations($course) && !$specialization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a specialization for this course.'
                ], 422);
            }
        }

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

        if ($specialization) {
            $query->whereHas('courseRegistrations', function ($q) use ($specialization) {
                $q->where('specialization', $specialization);
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
