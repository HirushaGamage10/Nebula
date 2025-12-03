<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseRegistration;
use App\Models\Intake;
use App\Models\Course;
use App\Models\Student;
use DB;

class CourseChangeController extends Controller
{
    public function index()
    {
        return view('course_change.index');
    }

    public function findStudent(Request $request)
{
    $student = Student::where('id_value', $request->nic)->first();

    if (!$student) {
        return response()->json(['status' => 'error', 'message' => 'Student not found']);
    }

    // Only future intakes OR today
    $today = now()->toDateString();

    $registrations = CourseRegistration::where('student_id', $student->student_id)
        ->where('status', 'Registered')
        ->with('course', 'intake')
        ->get()
        ->map(function($reg) use ($today) {
            $reg->is_future = $reg->course_start_date >= $today;
            return $reg;
        });

    return response()->json([
        'status' => 'success',
        'student' => $student,
        'registrations' => $registrations
    ]);
}


    public function getCourses()
    {
        return response()->json([
            'courses' => Course::select('course_id', 'course_name')->get()
        ]);
    }

    public function getNewIntakes(Request $request)
    {
        $intakes = Intake::where('course_id', $request->course_id)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'intakes' => $intakes
        ]);
    }

    // ========================= GENERATE NEW COURSE REG ID =========================
    public function generateNewCourseRegId(Request $request)
{
    $intake = Intake::find($request->intake_id);

    if (!$intake || !$intake->course_registration_id_pattern) {
        return response()->json(['status' => 'error', 'message' => 'Pattern not found']);
    }

    $pattern = $intake->course_registration_id_pattern;

    // Universal extraction: match text ending in digits
    if (!preg_match('/^(.*?)(\d+)$/', $pattern, $matches)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid pattern']);
    }

    $prefix = $matches[1]; // AI-DEV-
    $baseNum = $matches[2]; // 001
    $digits = strlen($baseNum);

    // Get existing IDs with same prefix
    $existing = CourseRegistration::where('course_registration_id', 'LIKE', $prefix . '%')
        ->pluck('course_registration_id')
        ->toArray();

    // If no matching IDs exist → return base pattern
    if (count($existing) == 0) {
        return response()->json([
            'status' => 'success',
            'new_id' => $pattern
        ]);
    }

    $max = 0;

    foreach ($existing as $id) {
        if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $id, $m)) {
            $num = intval($m[1]);
            if ($num > $max) {
                $max = $num;
            }
        }
    }

    // Compute next index
    $next = str_pad($max + 1, $digits, '0', STR_PAD_LEFT);

    return response()->json([
        'status' => 'success',
        'new_id' => $prefix . $next
    ]);
}


    // ========================= SUBMIT CHANGE =========================
    public function submitChange(Request $request)
    {
        $registration = CourseRegistration::find($request->registration_id);
        $newIntake = Intake::find($request->new_intake_id);

        if (!$registration || !$newIntake) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data']);
        }

        $oldIntake = $registration->intake_id;

        // Update course & intake
        $registration->course_id = $newIntake->course_id;
        $registration->intake_id = $newIntake->intake_id;
        $registration->course_start_date = $newIntake->start_date;
        $registration->course_registration_id = $request->new_course_registration_id;
        $registration->save();

        // Log record
        DB::table('course_change_logs')->insert([
            'student_id' => $registration->student_id,
            'old_intake_id' => $oldIntake,
            'new_intake_id' => $newIntake->intake_id,
            'changed_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course changed successfully'
        ]);
    }
}
