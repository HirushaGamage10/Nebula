<?php

namespace App\Http\Controllers;

use App\Models\ClearanceRequest;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Hostel;
use App\Models\Library;
use App\Models\PaymentClearance;
use App\Models\Project;
use App\Models\Student;
use Illuminate\Http\Request;

class AllClearanceController extends Controller
{
    public function showAllClearance(Request $request)
    {
        $student = null;
        $courses = Course::all(['course_id', 'course_name']);

        $allClearanceRequests = ClearanceRequest::with(['student', 'course', 'intake', 'approvedBy'])
            ->orderBy('requested_at', 'desc')
            ->get();

        $filteredRequests = $allClearanceRequests->filter(function ($item) {
            return $item->intake_id && $item->course_id && $item->location;
        });

        $groupedRequests = $filteredRequests->groupBy(function ($item) {
            return $item->intake_id . '-' . $item->course_id . '-' . $item->location . '-' . $item->clearance_type;
        });

        $intakeRequests = collect();
        foreach ($groupedRequests as $group) {
            $firstRequest  = $group->first();
            $totalStudents = $group->count();
            $approvedCount = $group->where('status', ClearanceRequest::STATUS_APPROVED)->count();
            $rejectedCount = $group->where('status', ClearanceRequest::STATUS_REJECTED)->count();
            $pendingCount  = $group->where('status', ClearanceRequest::STATUS_PENDING)->count();

            $intakeRequests->push((object) [
                'intake'         => $firstRequest->intake,
                'course'         => $firstRequest->course,
                'location'       => $firstRequest->location,
                'clearance_type' => $firstRequest->clearance_type,
                'total_students' => $totalStudents,
                'approved_count' => $approvedCount,
                'rejected_count' => $rejectedCount,
                'pending_count'  => $pendingCount,
                'received_count' => $approvedCount + $rejectedCount,
                'requested_at'   => $group->min('requested_at'),
                'latest_status'  => $group->sortByDesc('requested_at')->first()->status,
                'status_color'   => $group->sortByDesc('requested_at')->first()->status_color,
                'status_text'    => $group->sortByDesc('requested_at')->first()->status_text,
            ]);
        }

        $individualRequests = $allClearanceRequests->filter(function ($item) {
            return $item->is_individual_request;
        });

        $pendingRequests  = $allClearanceRequests->where('status', ClearanceRequest::STATUS_PENDING);
        $approvedRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_APPROVED);
        $rejectedRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_REJECTED);

        $student = null;
        if ($request->filled('student_id')) {
            $identifier = $request->input('student_id');
            $student = Student::where('student_id', $identifier)
                ->orWhere('id_value', $identifier)
                ->first();
        }

        return view('clearance.all_clearance', compact(
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
        $libraryRecords   = Library::where('student_id', $studentIdLibrary)->get();
        return view('all_clearance', compact('libraryRecords', 'studentIdLibrary'));
    }

    public function paymentsearch(Request $request)
    {
        $studentIdPayment = $request->get('student_id');
        $paymentRecords   = PaymentClearance::where('student_id', $studentIdPayment)->get();
        return view('all_clearance', compact('paymentRecords', 'studentIdPayment'));
    }

    public function hostelsearch(Request $request)
    {
        $studentId = $request->get('student_id');
        $records   = Hostel::where('student_id', $studentId)->get();
        return view('all_clearance', compact('records', 'studentId'));
    }

    public function projectsearch(Request $request)
    {
        $studentIdProject = $request->get('student_id');
        $projectRecords   = Project::where('student_id', $studentIdProject)->get();
        return view('all_clearance', compact('projectRecords', 'studentIdProject'));
    }

    public function sendClearance($type, $student_id)
    {
        return back()->with('success', ucfirst($type) . ' clearance form sent for student ID: ' . $student_id);
    }

    public function sendClearanceRequest(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:library,hostel,payment,project',
            'location'   => 'required|string',
            'course_id'  => 'required|exists:courses,course_id',
            'intake_id'  => 'required|exists:intakes,intake_id',
            'student_id' => 'nullable|exists:students,student_id',
        ]);

        try {
            $regQuery = CourseRegistration::where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->where('location', $request->location)
                ->eligible();

            if ($request->filled('student_id')) {
                $regQuery->where('student_id', $request->student_id);
            }

            $students = $regQuery->with('student')->get();

            $createdCount = 0;
            foreach ($students as $registration) {
                $existingPendingRequest = ClearanceRequest::where('student_id', $registration->student_id)
                    ->where('clearance_type', $request->type)
                    ->where('course_id', $request->course_id)
                    ->where('intake_id', $request->intake_id)
                    ->where('status', ClearanceRequest::STATUS_PENDING)
                    ->first();

                if (!$existingPendingRequest) {
                    ClearanceRequest::create([
                        'clearance_type'        => $request->type,
                        'location'              => $request->location,
                        'course_id'             => $request->course_id,
                        'intake_id'             => $request->intake_id,
                        'student_id'            => $registration->student_id,
                        'status'                => ClearanceRequest::STATUS_PENDING,
                        'requested_at'          => now(),
                        'is_individual_request' => $request->filled('student_id'),
                    ]);
                    $createdCount++;
                }
            }

            $totalCount   = $students->count();
            $skippedCount = $totalCount - $createdCount;

            return response()->json([
                'success' => true,
                'message' => "Clearance request(s) processed. Sent: {$createdCount}. Skipped (already pending): {$skippedCount}.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send clearance requests: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRegisteredCourses(Request $request)
    {
        $identifier = $request->query('nic') ?? $request->query('id_value') ?? $request->query('student_id');
        $student = \App\Models\Student::where('id_value', $identifier)
            ->orWhere('student_id', $identifier)
            ->first();
        if (!$student) {
            return response()->json(['success' => false, 'courses' => [], 'message' => 'Student not found']);
        }

        $registrations = \App\Models\CourseRegistration::where('student_id', $student->student_id)->get();
        $courses = [];
        foreach ($registrations as $reg) {
            if ($reg->course) {
                $courses[] = [
                    'id'   => $reg->course->course_id,
                    'name' => $reg->course->course_name,
                ];
            }
        }

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function getStudentCourseDetails(Request $request)
    {
        $identifier = $request->query('nic') ?? $request->query('id_value') ?? $request->query('student_id');
        $courseId   = $request->query('course_id');
        $student    = \App\Models\Student::where('id_value', $identifier)
            ->orWhere('student_id', $identifier)
            ->first();
        $course     = \App\Models\Course::find($courseId);

        if (!$student || !$course) {
            return response()->json(['success' => false, 'message' => 'Student or course not found']);
        }

        return response()->json([
            'success' => true,
            'name'    => $student->name_with_initials,
            'nic'     => $student->id_value,
            'course'  => $course->course_name,
        ]);
    }

    public function getStudentsForIntake(Request $request)
    {
        try {
            $intakeId = $request->input('intake_id');
            $courseId = $request->input('course_id');
            $location = $request->input('location');
            $query    = CourseRegistration::where('intake_id', $intakeId)
                ->whereHas('student', function ($q) {
                    $q->where('academic_status', 'active');
                })
                ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
                ->when($location, fn ($q) => $q->where('location', $location))
                ->with('student');

            $registrations = $query->get();

            $students = $registrations
                ->unique('student_id')
                ->map(function ($registration) use ($intakeId, $courseId, $location) {
                    $student = $registration->student;
                    if (!$student) {
                        return null;
                    }

                    $latestRequestQuery = ClearanceRequest::where('student_id', $student->student_id)
                        ->where('intake_id', $intakeId)
                        ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
                        ->when($location, fn ($q) => $q->where('location', $location));

                    $latest     = $latestRequestQuery->orderByDesc('requested_at')->first();
                    $statusText = $latest->status_text ?? ($latest->status ?? 'No Request');

                    return [
                        'student_id'       => $student->student_id,
                        'name'             => $student->name_with_initials ?? $student->name ?? ($student->full_name ?? ''),
                        'clearance_status' => $statusText,
                    ];
                })
                ->filter()
                ->values()
                ->all();

            return response()->json(['success' => true, 'data' => $students]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load students: ' . $e->getMessage()], 500);
        }
    }

    public function getIntakeDetails(Request $request)
    {
        $request->validate([
            'intake_id'     => 'required|exists:intakes,intake_id',
            'course_id'     => 'required|exists:courses,course_id',
            'location'      => 'required|string',
            'clearance_type' => 'required|string',
        ]);

        try {
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
                    'message' => 'No clearance requests found for the specified criteria.',
                ]);
            }

            $firstRequest = $clearanceRequests->first();
            $intakeName   = $firstRequest->intake->batch;
            $courseName   = $firstRequest->course->course_name;

            $students = $clearanceRequests->map(function ($item) {
                return [
                    'student_id'     => $item->student->student_id,
                    'student_name'   => $item->student->name_with_initials,
                    'status'         => $item->status,
                    'status_text'    => $item->status_text,
                    'status_color'   => $item->status_color,
                    'processed_by'   => $item->approvedBy->name ?? null,
                    'processed_date' => $item->approved_at ? $item->approved_at->format('d/m/Y H:i') : null,
                    'remarks'        => $item->remarks,
                ];
            });

            return response()->json([
                'success'     => true,
                'intake_name' => $intakeName,
                'course_name' => $courseName,
                'location'    => $request->location,
                'students'    => $students,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load intake details: ' . $e->getMessage(),
            ], 500);
        }
    }
}
