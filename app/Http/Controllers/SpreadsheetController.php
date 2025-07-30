<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Courses;
use App\Models\StudentCourseModule;
use App\Models\CourseRegistrations;
use App\Models\Students;
use App\Models\StoreAttendance;
use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class SpreadSheetController extends Controller
{
    public function showSpreadsheet(Request $request)
{
    // Validate the request query parameters
    $validatedData = $request->validate([
        'id' => 'required|string|max:40',
        'pin' => 'nullable|string|max:6',
    ]);

    // Retrieve 'id' and 'pin' from the validated data
    $id = $validatedData['id'];
    $pin = $validatedData['pin'] ?? null;

    // Check if the 'id' (form_link) exists in the database
    $attendance = Attendance::where('form_link', $id)->first();

    if (!$attendance) {
        // If the 'id' does not exist, abort with a 404 error
        abort(404);
    }

    // Check if the 'pin' is missing
    if (is_null($pin)) {
        // Pass data to the view indicating pin is missing
        return view('spreadsheet', [
            'pin' => false,
            'message' => 'Pin is missing.',
            'id' => $id,
        ]);
    }

    // Check if the 'pin' matches the one in the database
    if ($pin !== $attendance->form_pin) {
        // If the 'pin' does not match, return an error message
        return view('spreadsheet', [
            'pin' => false,
            'message' => 'Incorrect pin. Please try again.',
            'id' => $id,
        ]);
    }

    // If PIN is correct, proceed to retrieve and process data

    // Decode the form_content JSON string
    $formContent = json_decode($attendance->form_content, true);

    // Extract the values from the form content
    $courseId = $formContent['course'] ?? null;
    $batchId = $formContent['batch'] ?? null;
    $moduleId = $formContent['module'] ?? null;
    $moduleYear = $formContent['moduleYear'] ?? null;
    $semesterId = $formContent['semester'] ?? null;

    // Retrieve the course name using the course ID
    $course = Courses::where('course_id', $courseId)->first();

    if (!$course) {
        // If the course does not exist, abort with a 404 error
        abort(404, 'Course not found.');
    }

    // Get the course name
    $courseName = $course->course_name;

    // Retrieve the module name using the module ID
    $module = Module::where('module_id', $moduleId)->first();

    if (!$module) {
        // If the module does not exist, abort with a 404 error
        abort(404, 'Module not found.');
    }

    // Get the module name
    $moduleName = $module->module_name;

    // Retrieve the user_id from the Attendance record
    $userId = $attendance->user_id;

    // Retrieve the user's name using the user_id
    $userName = '';
    if ($userId) {
        $user = User::where('user_id', $userId)->first();
        if ($user) {
            $userName = $user->name;
        }
    }

    // Count the number of similar form_content entries in the attendance table
    $similarContentCount = Attendance::where('form_content', $attendance->form_content)->count();

    // Get the list of student IDs that match the criteria from the StudentCourseModule table
    $studentIds = StudentCourseModule::where([
        ['course_id', $courseId],
        ['batch_id', $batchId],
        ['module_id', $moduleId],
        ['semester_id', $semesterId],
    ])->pluck('student_id');

    // Get the student_course_registrationID from the course_registrations table and name_with_initials from students table
    $studentRegistrations = CourseRegistrations::whereIn('student_id', $studentIds)
        ->where('course_id', $courseId)
        ->where('batch', $batchId)
        ->get(['student_id', 'student_course_registrationID']);

    $students = $studentRegistrations->map(function ($registration) {
        $student = Students::find($registration->student_id);
        return [
            'student_id' => $registration->student_id,
            'regNo' => $registration->student_course_registrationID,
            'name' => $student ? $student->name_with_initials : null,
        ];
    });

    // Pass data to the view
    return view('spreadsheet', [
        'pin' => true,
        'id' => $id,
        'pin' => $pin,
        'courseName' => $courseName,
        'moduleName' => $moduleName,
        'userName' => $userName,
        'days' => $similarContentCount + 1,
        'students' => $students,
    ]);
}






    // Storing attendance
    public function storeAttendance(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate([
                'lectureName' => 'required|string|max:255',
                'lectureRoom' => 'nullable|string|max:255',
                'markedByName' => 'required|string|max:255',
                'id' => 'required|string|max:40',
                'pin' => 'required|string|max:6',
                'attendance' => 'required|array',
            ]);

            // Extract inputs
            $lectureName = $request->input('lectureName');
            $markedByName = $request->input('markedByName');
            $lectureRoom = $request->input('lectureRoom');
            $id = $request->input('id');
            $pin = $request->input('pin');
            $attendanceData = $request->input('attendance');

            // Check if form_link and form_pin are valid
            $attendanceRecord = Attendance::where('form_link', $id)
                ->where('form_pin', $pin)
                ->first();

            if (!$attendanceRecord) {
                return response()->json(['success' => false, 'message' => 'Invalid ID or PIN.']);
            }

            // Parse form_content JSON data
            $formContent = json_decode($attendanceRecord->form_content, true);
            if (!$formContent) {
                return response()->json(['success' => false, 'message' => 'Invalid form content data.']);
            }

            // Extract course_id, batch_id, module_id, and class_date
            $courseId = $formContent['course'] ?? null;
            $batchId = $formContent['batch'] ?? null;
            $moduleId = $formContent['module'] ?? null;
            $classDate = $attendanceRecord->class_date;

            // Update conduct_by, lecture_room, and saved_times
            $attendanceRecord->conduct_by = $lectureName;
            $attendanceRecord->lecture_room = $lectureRoom;
            $attendanceRecord->markedBy = $markedByName;
            $attendanceRecord->saved_times += 1; // Increment saved_times

            // Save the updated record
            $attendanceRecord->save();

            // Save attendance data in the store_attendance table
            foreach ($attendanceData as $attendance) {
                $studentId = $attendance['student_id'];
                $inTime = $attendance['timeIn'];
                $outTime = $attendance['timeOut'];

                // Determine if the student is attended
                $isAttended = !is_null($inTime);

                // Check if the student ID is valid
                $studentExists = Students::where('student_id', $studentId)->exists();

                if (!$studentExists) {
                    // If the student ID is not valid, skip this record
                    continue;
                }

                // Check if the record already exists
                $existingRecord = StoreAttendance::where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->where('batch_id', $batchId)
                    ->where('module_id', $moduleId)
                    ->where('class_date', $classDate)
                    ->where('link_ref_id', $attendanceRecord->id)
                    ->first();

                if ($existingRecord) {
                    // If the record exists, update it
                    $existingRecord->is_attended = $isAttended;
                    $existingRecord->inOutTime = json_encode([
                        'inTime' => $inTime,
                        'outTime' => $outTime
                    ]);
                    $existingRecord->save();
                } else {
                    // Create new attendance record
                    StoreAttendance::create([
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'batch_id' => $batchId,
                        'module_id' => $moduleId,
                        'class_date' => $classDate,
                        'is_attended' => $isAttended,
                        'link_ref_id' => $attendanceRecord->id, // Reference to the main attendance record
                        'inOutTime' => json_encode([
                            'inTime' => $inTime,
                            'outTime' => $outTime
                        ]),
                    ]);
                }
            }

            // Return success response
            return response()->json(['success' => true, 'message' => 'Attendance successfully stored.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Internal Server Error.']);
        }
    }
}
