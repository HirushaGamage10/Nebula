<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Student;
use App\Models\Intake;
use App\Models\CourseRegistration;
use App\Models\StudentExam;
use App\Models\PaymentDetail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseRegistraionController extends Controller
{
    public function checkStudents(Request $request)
    {
        try {
            // Get the courseId and studentIds from the request
            $courseId = $request->input('courseId');
            $studentIds = $request->input('studentIds');

            // Initialize an array to store the registration status of each student
            $studentStatus = [];

            // Loop through each student ID
            foreach ($studentIds as $studentId) {
                // Check if the student is registered for the specified course
                $registered = CourseRegistration::where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->exists();

                // Push the student ID and registration status to the array
                $studentStatus[] = [
                    'student_id' => $studentId,
                    'registered' => $registered ? true : false,
                ];
            }

            // Return the student IDs and registration status as JSON response
            return response()->json(['success' => true, 'data' => $studentStatus]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error checking students: ' . $e->getMessage());

            // Return an error response
            return response()->json(['success' => false, 'message' => 'An error occurred while checking students']);
        }
    }

    public function showCourseRegistration(Request $request)
    {
        // Fetch the student exam record (adjust as per your logic)
        $studentExam = StudentExam::where('student_id', $request->student_id)->first();

        // Default to empty array if not found
        $olSubjects = [];
        $alSubjects = [];

        if ($studentExam) {
            // First decode to get the string, then decode again to get the array
            $olSubjectsRaw = json_decode($studentExam->ol_exam_subjects, true);
            if (is_string($olSubjectsRaw)) {
                $olSubjects = json_decode($olSubjectsRaw, true) ?? [];
            } else {
                $olSubjects = $olSubjectsRaw ?? [];
            }

            $alSubjectsRaw = json_decode($studentExam->al_exam_subjects, true);
            if (is_string($alSubjectsRaw)) {
                $alSubjects = json_decode($alSubjectsRaw, true) ?? [];
            } else {
                $alSubjects = $alSubjectsRaw ?? [];
            }
        }

        // Pass to view
        return view('course_registration', [
            // ...other variables...
            'olSubjects' => $olSubjects,
            'alSubjects' => $alSubjects,
        ]);
    }

    public function getCoursesByLocation($location)
    {
        if (!$location) {
            return response()->json(['error' => 'Location is required.'], 400);
        }
        try {
            $courses = Course::select('course_id', 'course_name')
                ->where('location', $location)
                ->orderBy('course_name', 'asc')
                ->get();

            if ($courses->isEmpty()) {
                return response()->json(['error' => 'No courses found for this location.']);
            }

            \Log::info('Requested location:', ['location' => $location]);
            \Log::info('Courses found:', ['courses' => $courses]);

            return response()->json(['success' => true, 'courses' => $courses]);
        } catch (\Exception $e) {
            \Log::error('Error fetching courses by location: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching courses.'], 500);
        }
    }

    public function batchDropdownOptions(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'registered_from' => 'required|date',
            'registered_to' => 'required|date',
            'courseSearch' => 'required|exists:courses,course_id',
        ]);

        // Retrieve the date range and course ID from the request
        $registeredFrom = $request->input('registered_from');
        $registeredTo = $request->input('registered_to');
        $courseId = $request->input('courseSearch');

        // Retrieve all intake details relevant to the given course
        $intakeDetails = Intake::where('course_id', $courseId)->get();

        Log::info('Intake details retrieved', ['courseId' => $courseId, 'intakeDetails' => $intakeDetails->toArray()]);

        $intakesForCourse = [];

        if ($intakeDetails->isNotEmpty()) {
            // Return intake details
            $intakesForCourse = $intakeDetails->toArray();
            Log::info('Intakes for course retrieved', ['intakesForCourse' => $intakesForCourse]);
        } else {
            Log::info('No intake details found for the given course and date range.');
        }

        return response()->json([
            'intakesForCourse' => $intakesForCourse,
        ]);
    }

    public function checkBlacklistStatus(Request $request)
    {
        $indexNo = $request->query('index_no');

        if (!$indexNo) {
            Log::warning('Missing index number in request');
            return response()->json(['error' => 'Index number is required'], 400);
        }

        try {
            $isBlacklisted = DB::table('students')
                ->where('registration_id', $indexNo)
                ->value('is_blacklisted');

            if ($isBlacklisted === null) {
                Log::info('Student not found in database:', ['index_no' => $indexNo]);
                return response()->json(['error' => 'Student not found'], 404);
            }

            Log::info('Blacklist status retrieved:', [
                'index_no' => $indexNo,
                'is_blacklisted' => $isBlacklisted,
            ]);

            return response()->json([
                'is_blacklisted' => (int)$isBlacklisted === 1, // Compare value
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching blacklist status:', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'An error occurred while checking blacklist status.'], 500);
        }
    }

    public function checkStudentExists(Request $request)
    {
        if (Auth::check() && Auth::user()->status) {
            try {
                // Validate the incoming request data
                $request->validate([
                    'registered_from' => 'required|date',
                    'registered_to' => 'required|date',
                    'courseSearch' => 'required|exists:courses,course_id',
                ]);

                // Retrieve the date range and course ID from the request
                $registeredFrom = $request->input('registered_from');
                $registeredTo = $request->input('registered_to');
                $courseId = $request->input('courseSearch');
                $userLocation = Auth::user()->user_location; // Get the user's location

                // Check if the registeredFrom and registeredTo dates are the same
                if ($registeredFrom == $registeredTo) {
                    // Fetch students created on the specific date and in the user's location
                    $students = Student::whereDate('created_at', $registeredFrom)
                        ->where('institute_location', $userLocation)
                        ->select('student_id', 'registration_id', 'name_with_initials', 'id_type', 'id_value')
                        ->get();
                } else {
                    // Fetch students created within the specified date range and in the user's location
                    $students = Student::whereBetween('created_at', [$registeredFrom, $registeredTo])
                        ->where('institute_location', $userLocation)
                        ->select('student_id', 'registration_id', 'name_with_initials', 'id_type', 'id_value')
                        ->get();
                }

                // Check if each student is registered for the course
                $students->each(function ($student) use ($courseId) {
                    $student->registered = CourseRegistration::where('student_id', $student->student_id)
                        ->where('course_id', $courseId)
                        ->exists() ? 'yes' : 'no';
                });

                // Count the number of students
                $studentCount = $students->count();

                // Check if student count is 0
                if ($studentCount === 0) {
                    // Return a JSON response with success false and custom message
                    return response()->json([
                        'success' => false,
                        'message' => 'Students not found',
                    ]);
                }

                // Prepare the response data
                $responseData = [
                    'success' => true,
                    'message' => 'Students found',
                    'students' => $students,
                    'studentCount' => $studentCount, // Include the student count in the response
                ];

                // Return the JSON response
                return response()->json($responseData);
            } catch (\Exception $e) {
                // Log the error with detailed information
                Log::error('Error in checkStudentExists method: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);

                // Return a generic error message
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred. Please try again later.'
                ]);
            }
        } else {
            // Redirect to login or show an error message
            return redirect()->route('login')->with('error', 'You are not authorized to access this page.');
        }
    }

    public function storeCourseRegistration(Request $request)
    {
        if (Auth::check() && Auth::user()->status) {
            try {
                // Validate the incoming request data
                $validatedData = $request->validate([
                    'studentId' => 'required|exists:students,student_id',
                    'course' => 'required|exists:courses,course_id',
                    'location' => 'required|in:Nebula Institute of Technology - Welisara,Moratuwa,Peradeniya',
                    'sltEmployee' => 'required|string|in:yes,no',
                    'serviceNo' => 'nullable|string|max:255',
                    'options' => 'required|string',
                    'surveyNo' => 'required|numeric',
                    'registrationFee' => 'required|numeric',
                    'courseStartDate' => 'required|date',
                ]);

                // Check if the student is already registered for the course
                $isAlreadyRegistered = CourseRegistration::where('student_id', $validatedData['studentId'])
                    ->where('course_id', $validatedData['course'])
                    ->exists();

                if ($isAlreadyRegistered) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The student is already registered for this course.',
                    ], 400);
                }

                // Create a new CourseRegistration instance
                $courseRegistration = new CourseRegistration();

                // Assign the validated data to the CourseRegistration instance
                $courseRegistration->student_id = $validatedData['studentId'];
                $courseRegistration->course_id = $validatedData['course'];
                $courseRegistration->registration_date = now();
                $courseRegistration->registration_fee = $validatedData['registrationFee'];
                $courseRegistration->status = 'Pending';
                $courseRegistration->approval_status = 'Pending';
                $courseRegistration->location = $validatedData['location'];
                $courseRegistration->slt_employee = ($validatedData['sltEmployee'] === 'yes') ? 1 : 0;
                $courseRegistration->employee_service_number = $validatedData['serviceNo'];
                $courseRegistration->remarks = 'Pre-registration via web form';

                // Save the CourseRegistration instance
                $courseRegistration->save();

                // Update student's marketing survey
                $student = Student::find($validatedData['studentId']);
                if ($student) {
                    $student->marketing_survey = $validatedData['options'];
                    $student->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Course registration completed successfully.',
                    'registration_id' => $courseRegistration->id
                ]);

            } catch (\Exception $e) {
                Log::error('Error in storeCourseRegistration: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the registration.'
                ], 500);
            }
        } else {
            return redirect()->route('login')->with('error', 'You are not authorized to access this page.');
        }
    }

    // API method to get student by NIC
    public function getStudentByNic($nic)
    {
        try {
            // Find student by NIC (id_value)
            $student = Student::where('id_value', $nic)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Get student exam details
            $studentExam = StudentExam::where('student_id', $student->student_id)->first();

            // Prepare O/L exam data
            $ol_exams = [];
            if ($studentExam && $studentExam->ol_exam_type) {
                $ol_exams[] = [
                    'exam_type' => ['exam_type' => $studentExam->ol_exam_type],
                    'exam_year' => $studentExam->ol_exam_year,
                    'subjects' => $studentExam->ol_exam_subjects ? json_decode($studentExam->ol_exam_subjects, true) : []
                ];
            }

            // Prepare A/L exam data
            $al_exams = [];
            if ($studentExam && $studentExam->al_exam_type) {
                $al_exams[] = [
                    'exam_type' => ['exam_type' => $studentExam->al_exam_type],
                    'exam_year' => $studentExam->al_exam_year,
                    'stream' => ['stream' => $studentExam->al_exam_stream],
                    'z_score' => $studentExam->z_score_value,
                    'subjects' => $studentExam->al_exam_subjects ? json_decode($studentExam->al_exam_subjects, true) : []
                ];
            }

            return response()->json([
                'success' => true,
                'student' => [
                    'student_id' => $student->student_id,
                    'name_with_initials' => $student->name_with_initials,
                    'id_value' => $student->id_value,
                    'registration_id' => $student->student_id // Using student_id as registration_id for compatibility
                ],
                'ol_exams' => $ol_exams,
                'al_exams' => $al_exams
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting student by NIC: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching student data'
            ], 500);
        }
    }

    // API method to get intakes by course
    public function getIntakesByCourse($courseId)
    {
        try {
            $intakes = Intake::where('course_id', $courseId)
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'intakes' => $intakes
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting intakes by course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching intakes'
            ], 500);
        }
    }

    // Updated API method for course registration
    public function storeCourseRegistrationAPI(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'student_id' => 'required|exists:students,student_id',
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'slt_employee' => 'required|in:yes,no',
                'service_no' => 'nullable|string|max:255',
                'counselor_name' => 'nullable|string|max:255',
                'counselor_id' => 'nullable|string|max:255',
                'counselor_phone' => 'nullable|string|max:255',
                'counselor_nic' => 'nullable|string|max:255',
                'marketing_survey_options' => 'required|array',
                'course_start_date' => 'required|date',
                'registration_fee' => 'required|numeric|min:0',
            ]);

            // Check if the student is already registered for the course
            $isAlreadyRegistered = CourseRegistration::where('student_id', $validatedData['student_id'])
                ->where('course_id', $validatedData['course_id'])
                ->exists();

            if ($isAlreadyRegistered) {
                return response()->json([
                    'success' => false,
                    'message' => 'The student is already registered for this course.'
                ], 400);
            }

            // Create a new CourseRegistration instance
            $courseRegistration = new CourseRegistration();

            // Assign the validated data to the CourseRegistration instance
            $courseRegistration->student_id = $validatedData['student_id'];
            $courseRegistration->course_id = $validatedData['course_id'];
            $courseRegistration->intake_id = $validatedData['intake_id'];
            $courseRegistration->registration_date = now();
            $courseRegistration->registration_fee = $validatedData['registration_fee'];
            $courseRegistration->status = 'Pending';
            $courseRegistration->approval_status = 'Pending';
            $courseRegistration->location = $validatedData['location'];
            $courseRegistration->slt_employee = ($validatedData['slt_employee'] === 'yes') ? 1 : 0;
            $courseRegistration->employee_service_number = $validatedData['service_no'];
            $courseRegistration->course_start_date = $validatedData['course_start_date'];
            $courseRegistration->remarks = 'Pre-registration via web form';

            // Add counselor information if SLT employee is 'no'
            if ($validatedData['slt_employee'] === 'no') {
                $courseRegistration->counselor_name = $validatedData['counselor_name'] ?? null;
                $courseRegistration->counselor_id = $validatedData['counselor_id'] ?? null;
                $courseRegistration->counselor_phone = $validatedData['counselor_phone'] ?? null;
                $courseRegistration->counselor_nic = $validatedData['counselor_nic'] ?? null;
            }

            // Save the CourseRegistration instance
            $courseRegistration->save();

            // Update student's marketing survey - join array with comma
            $student = Student::find($validatedData['student_id']);
            if ($student) {
                $marketingSurvey = implode(', ', $validatedData['marketing_survey_options']);
                $student->marketing_survey = $marketingSurvey;
                $student->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Course registration completed successfully.',
                'registration_id' => $courseRegistration->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error in storeCourseRegistrationAPI: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the registration.'
            ], 500);
        }
    }

    public function getYears()
    {
        // Example: Return years from 2020 to current year
        $years = range(2020, date('Y'));
        return response()->json(['years' => $years]);
    }

    public function getSemestersByYear(Request $request)
    {
        $year = $request->query('year');
        // Example: Return semesters for the given year
        $semesters = ['Semester 1', 'Semester 2']; // Replace with your logic
        return response()->json(['semesters' => $semesters]);
    }

    public function getIntakes(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string',
            'location' => 'required|string',
        ]);

        $courseName = $request->input('course_name');
        $location = $request->input('location');

        $intakes = \App\Models\Intake::where('course_name', $courseName)
            ->where('location', $location)
            ->orderBy('start_date', 'asc')
            ->get(['intake_id', 'batch', 'start_date', 'registration_fee']);

        return response()->json($intakes);
    }

    // API method for saving course registration for eligibility checking
    public function storeCourseRegistrationForEligibilityAPI(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'student_id' => 'required|exists:students,student_id',
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'slt_employee' => 'required|in:yes,no',
                'service_no' => 'nullable|string|max:255',
                'counselor_name' => 'nullable|string|max:255',
                'counselor_id' => 'nullable|string|max:255',
                'counselor_phone' => 'nullable|string|max:255',
                'counselor_nic' => 'nullable|string|max:255',
                'marketing_survey_options' => 'required|array',
                'course_start_date' => 'required|date',
                'registration_fee' => 'required|numeric|min:0',
            ]);

            // Check if the student is already registered for the course
            $isAlreadyRegistered = CourseRegistration::where('student_id', $validatedData['student_id'])
                ->where('course_id', $validatedData['course_id'])
                ->exists();

            if ($isAlreadyRegistered) {
                return response()->json([
                    'success' => false,
                    'message' => 'The student is already registered for this course.'
                ], 400);
            }

            // Create a new CourseRegistration instance
            $courseRegistration = new CourseRegistration();

            // Assign the validated data to the CourseRegistration instance
            $courseRegistration->student_id = $validatedData['student_id'];
            $courseRegistration->course_id = $validatedData['course_id'];
            $courseRegistration->intake_id = $validatedData['intake_id'];
            $courseRegistration->registration_date = now();
            $courseRegistration->registration_fee = $validatedData['registration_fee'];
            $courseRegistration->status = 'Pending';
            $courseRegistration->approval_status = 'Pending';
            $courseRegistration->location = $validatedData['location'];
            $courseRegistration->slt_employee = ($validatedData['slt_employee'] === 'yes') ? 1 : 0;
            $courseRegistration->employee_service_number = $validatedData['service_no'];
            $courseRegistration->course_start_date = $validatedData['course_start_date'];
            $courseRegistration->remarks = 'Pre-registration via web form for eligibility checking';

            // Add counselor information if SLT employee is 'no'
            if ($validatedData['slt_employee'] === 'no') {
                $courseRegistration->counselor_name = $validatedData['counselor_name'] ?? null;
                $courseRegistration->counselor_id = $validatedData['counselor_id'] ?? null;
                $courseRegistration->counselor_phone = $validatedData['counselor_phone'] ?? null;
                $courseRegistration->counselor_nic = $validatedData['counselor_nic'] ?? null;
            }

            // Save the CourseRegistration instance
            $courseRegistration->save();

            // Update student's marketing survey - join array with comma
            $student = Student::find($validatedData['student_id']);
            if ($student) {
                $marketingSurvey = implode(', ', $validatedData['marketing_survey_options']);
                $student->marketing_survey = $marketingSurvey;
                $student->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Course registration saved for eligibility checking.',
                'registration_id' => $courseRegistration->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error in storeCourseRegistrationForEligibilityAPI: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the registration.'
            ], 500);
        }
    }
}
