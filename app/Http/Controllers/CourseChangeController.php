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

        $registrations = CourseRegistration::where('student_id', $student->student_id)
            ->where('status', 'Registered')
            ->with('course', 'intake')
            ->get();

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

    public function submitChange(Request $request)
    {
        $registration = CourseRegistration::find($request->registration_id);
        $newIntake = Intake::find($request->new_intake_id);

        if (!$registration || !$newIntake) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data']);
        }

        $oldIntake = $registration->intake_id;

        $registration->course_id = $newIntake->course_id;
        $registration->intake_id = $newIntake->intake_id;
        $registration->course_start_date = $newIntake->start_date;

        $registration->save();

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
