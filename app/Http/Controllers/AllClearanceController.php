<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Library;
use App\Models\PaymentClearance;
use App\Models\Project;
use App\Models\Student;
use App\Models\Course;
use App\Models\ClearanceRequest;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;

class AllClearanceController extends Controller
{
    public function showAllClearance(Request $request)
    {
        $student = null;
        $courses = Course::all(['course_id', 'course_name']);
        
        // Get all clearance requests for status tracking
        $allClearanceRequests = ClearanceRequest::with(['student', 'course', 'intake', 'approvedBy'])
            ->orderBy('requested_at', 'desc')
            ->get();

        // Group intake requests by intake and calculate summary statistics
        $intakeRequests = collect();
        
        $filteredRequests = $allClearanceRequests->filter(function($request) {
            return $request->intake_id && $request->course_id && $request->location;
        });
        
        // Group by unique combination of intake, course, location, and clearance type
        $groupedRequests = $filteredRequests->groupBy(function($request) {
            return $request->intake_id . '-' . $request->course_id . '-' . $request->location . '-' . $request->clearance_type;
        });
        
        foreach ($groupedRequests as $group) {
            $firstRequest = $group->first();
            $totalStudents = $group->count();
            $approvedCount = $group->where('status', ClearanceRequest::STATUS_APPROVED)->count();
            $rejectedCount = $group->where('status', ClearanceRequest::STATUS_REJECTED)->count();
            $pendingCount = $group->where('status', ClearanceRequest::STATUS_PENDING)->count();
            
            $intakeRequests->push((object) [
                'intake' => $firstRequest->intake,
                'course' => $firstRequest->course,
                'location' => $firstRequest->location,
                'clearance_type' => $firstRequest->clearance_type,
                'total_students' => $totalStudents,
                'approved_count' => $approvedCount,
                'rejected_count' => $rejectedCount,
                'pending_count' => $pendingCount,
                'received_count' => $approvedCount + $rejectedCount,
                'requested_at' => $group->min('requested_at'),
                'latest_status' => $group->sortByDesc('requested_at')->first()->status,
                'status_color' => $group->sortByDesc('requested_at')->first()->status_color,
                'status_text' => $group->sortByDesc('requested_at')->first()->status_text,
            ]);
        }

        $individualRequests = $allClearanceRequests->filter(function($request) {
            // Individual requests are those sent for specific students
            // These would be sent via the individual clearance tab
            return $request->student_id && !$request->intake_id;
        });

        // Group requests by status for the status tab
        $pendingRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_PENDING);
        $approvedRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_APPROVED);
        $rejectedRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_REJECTED);

        if ($request->has('student_id')) {
            $student = Student::where('student_id', $request->student_id)
                            ->orWhere('nic', $request->student_id)
                            ->first();
        }
        
        return view('all_clearance', compact(
            'student', 
            'courses', 
            'allClearanceRequests', 
            'pendingRequests', 
            'approvedRequests', 
            'rejectedRequests',
            'intakeRequests',
            'individualRequests'
        ));
    }

    public function librarysearch(Request $request)
    {
        $studentIdLibrary = $request->get('student_id');
        $libraryRecords = Library::where('student_id', $studentIdLibrary)->get();
        return view('all_clearance', compact('libraryRecords', 'studentIdLibrary'));
    }

    public function paymentsearch(Request $request)
    {
        $studentIdPayment = $request->get('student_id');
        $paymentRecords = PaymentClearance::where('student_id', $studentIdPayment)->get();
        return view('all_clearance', compact('paymentRecords', 'studentIdPayment'));
    }


    public function hostelsearch(Request $request)
    {
        $studentId = $request->get('student_id');
        $records = Hostel::where('student_id', $studentId)->get();
        return view('all_clearance', compact('records', 'studentId'));
    }

    public function projectsearch(Request $request)
    {
        $studentIdProject = $request->get('student_id');
        $projectRecords = Project::where('student_id', $studentIdProject)->get();
        return view('all_clearance', compact('projectRecords', 'studentIdProject'));
    }

    public function sendClearance($type, $student_id)
    {
        // You can implement logic here, like logging the request, updating DB, or redirecting
        return back()->with('success', ucfirst($type).' clearance form sent for student ID: '.$student_id);
    }

    /**
     * Handle AJAX clearance request and send notification
     */
    public function sendClearanceRequest(Request $request)
    {
        $request->validate([
            'type' => 'required|in:library,hostel,payment,project',
            'location' => 'required|string',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
        ]);

        try {
            // Get all students registered for this course and intake
            $students = CourseRegistration::where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->where('location', $request->location)
                ->where(function($query) {
                    $query->where('status', 'Registered')
                          ->orWhere('approval_status', 'Approved by DGM');
                })
                ->with('student')
                ->get();

            $createdCount = 0;
            foreach ($students as $registration) {
                // Check if clearance request already exists
                $existingRequest = ClearanceRequest::where('student_id', $registration->student_id)
                    ->where('clearance_type', $request->type)
                    ->where('course_id', $request->course_id)
                    ->where('intake_id', $request->intake_id)
                    ->first();

                if (!$existingRequest) {
                    ClearanceRequest::create([
                        'clearance_type' => $request->type,
                        'location' => $request->location,
                        'course_id' => $request->course_id,
                        'intake_id' => $request->intake_id,
                        'student_id' => $registration->student_id,
                        'status' => ClearanceRequest::STATUS_PENDING,
                        'requested_at' => now(),
                    ]);
                    $createdCount++;
                }
            }

            return response()->json([
                'success' => true, 
                'message' => "Clearance requests sent successfully! {$createdCount} students notified."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send clearance requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Get registered courses for a student NIC
     */
    public function getRegisteredCourses(Request $request)
    {
        $nic = $request->query('nic');
        $student = \App\Models\Student::where('id_value', $nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'courses' => [], 'message' => 'Student not found']);
        }
        $registrations = \App\Models\CourseRegistration::where('student_id', $student->student_id)->get();
        $courses = [];
        foreach ($registrations as $reg) {
            if ($reg->course) {
                $courses[] = [
                    'id' => $reg->course->course_id,
                    'name' => $reg->course->course_name
                ];
            }
        }
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    /**
     * AJAX: Get student name, NIC, and course name for a given NIC and course_id
     */
    public function getStudentCourseDetails(Request $request)
    {
        $nic = $request->query('nic');
        $courseId = $request->query('course_id');
        $student = \App\Models\Student::where('id_value', $nic)->first();
        $course = \App\Models\Course::find($courseId);
        if (!$student || !$course) {
            return response()->json(['success' => false, 'message' => 'Student or course not found']);
        }
        return response()->json([
            'success' => true,
            'name' => $student->name_with_initials,
            'nic' => $student->id_value,
            'course' => $course->course_name
        ]);
    }

    /**
     * Handle AJAX request to get students for an intake (mock implementation)
     */
    public function getStudentsForIntake(Request $request)
    {
        // TODO: Replace with real DB query for students in the intake
        $students = [
            ['student_id' => 'S001', 'name' => 'John Doe', 'clearance_status' => 'Pending'],
            ['student_id' => 'S002', 'name' => 'Jane Smith', 'clearance_status' => 'Pending'],
            ['student_id' => 'S003', 'name' => 'Alice Johnson', 'clearance_status' => 'Cleared'],
        ];
        return response()->json(['success' => true, 'data' => $students]);
    }

    /**
     * Get detailed student information for a specific intake clearance request
     */
    public function getIntakeDetails(Request $request)
    {
        $request->validate([
            'intake_id' => 'required|exists:intakes,intake_id',
            'course_id' => 'required|exists:courses,course_id',
            'location' => 'required|string',
            'clearance_type' => 'required|string',
        ]);

        try {
            // Get all clearance requests for this specific intake, course, location, and type
            $clearanceRequests = ClearanceRequest::where('intake_id', $request->intake_id)
                ->where('course_id', $request->course_id)
                ->where('location', $request->location)
                ->where('clearance_type', $request->clearance_type)
                ->with(['student', 'course', 'intake', 'approvedBy'])
                ->orderBy('requested_at', 'desc')
                ->get();

            if ($clearanceRequests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No clearance requests found for the specified criteria.'
                ]);
            }

            // Get intake and course information
            $firstRequest = $clearanceRequests->first();
            $intakeName = $firstRequest->intake->batch;
            $courseName = $firstRequest->course->course_name;

            // Format student data for the response
            $students = $clearanceRequests->map(function($request) {
                return [
                    'student_id' => $request->student->student_id,
                    'student_name' => $request->student->name_with_initials,
                    'status' => $request->status,
                    'status_text' => $request->status_text,
                    'status_color' => $request->status_color,
                    'processed_by' => $request->approvedBy->name ?? null,
                    'processed_date' => $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : null,
                    'remarks' => $request->remarks,
                ];
            });

            return response()->json([
                'success' => true,
                'intake_name' => $intakeName,
                'course_name' => $courseName,
                'location' => $request->location,
                'students' => $students
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load intake details: ' . $e->getMessage()
            ], 500);
        }
    }
}
