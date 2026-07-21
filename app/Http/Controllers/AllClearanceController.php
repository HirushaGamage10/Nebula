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
    private function normalizeSpecializationValue($value)
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function courseHasSpecializations($course): bool
    {
        if (!$course || empty($course->specializations)) {
            return false;
        }

        $specializations = is_array($course->specializations)
            ? $course->specializations
            : json_decode($course->specializations, true);

        return is_array($specializations) && count(array_filter($specializations)) > 0;
    }

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
            return $item->intake_id . '-' . $item->course_id . '-' . ($item->specialization ?: 'all') . '-' . $item->location . '-' . $item->clearance_type;
        });

        $intakeRequests = collect();
        foreach ($groupedRequests as $group) {
            $firstRequest = $group->first();
            $totalStudents = $group->count();
            $approvedCount = $group->where('status', ClearanceRequest::STATUS_APPROVED)->count();
            $rejectedCount = $group->where('status', ClearanceRequest::STATUS_REJECTED)->count();
            $pendingCount = $group->where('status', ClearanceRequest::STATUS_PENDING)->count();

            $intakeRequests->push((object) [
                'intake' => $firstRequest->intake,
                'course' => $firstRequest->course,
                'specialization' => $firstRequest->specialization,
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

        $individualRequests = $allClearanceRequests->filter(function ($item) {
            return $item->is_individual_request;
        });

        $pendingRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_PENDING);
        $approvedRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_APPROVED);
        $rejectedRequests = $allClearanceRequests->where('status', ClearanceRequest::STATUS_REJECTED);

        if ($request->has('student_id')) {
            $student = Student::where('student_id', $request->student_id)
                ->orWhere('nic', $request->student_id)
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
        return back()->with('success', ucfirst($type) . ' clearance form sent for student ID: ' . $student_id);
    }

    public function sendClearanceRequest(Request $request)
    {
        $request->validate([
            'type' => 'required|in:library,hostel,payment,project',
            'location' => 'required|string',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'specialization' => 'nullable|string',
            'student_id' => 'nullable|exists:students,student_id',
        ]);

        try {
            $course = Course::find($request->course_id);
            $specialization = $this->normalizeSpecializationValue($request->specialization);

            if ($this->courseHasSpecializations($course) && !$specialization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a specialization for this course.'
                ], 422);
            }

            $regQuery = CourseRegistration::where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->where('location', $request->location)
                ->where(function ($query) {
                    $query->where('status', 'Registered')
                        ->orWhere('approval_status', 'Approved by DGM');
                });

            if ($specialization) {
                $regQuery->where(function ($query) use ($specialization) {
                    $query->where('specialization', $specialization)
                        ->orWhereHas('student.semesterRegistrations', function ($semesterQuery) use ($specialization) {
                            $semesterQuery->where('specialization', $specialization);
                        })
                        ->orWhereHas('student.moduleManagements', function ($moduleQuery) use ($specialization) {
                            $moduleQuery->where('specialization', $specialization);
                        });
                });
            }

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
                    ->when($specialization, function ($query) use ($specialization) {
                        $query->where('specialization', $specialization);
                    })
                    ->where('status', ClearanceRequest::STATUS_PENDING)
                    ->first();

                if (!$existingPendingRequest) {
                    ClearanceRequest::create([
                        'clearance_type' => $request->type,
                        'location' => $request->location,
                        'course_id' => $request->course_id,
                        'intake_id' => $request->intake_id,
                        'specialization' => $specialization,
                        'student_id' => $registration->student_id,
                        'status' => ClearanceRequest::STATUS_PENDING,
                        'requested_at' => now(),
                        'is_individual_request' => $request->filled('student_id'),
                    ]);
                    $createdCount++;
                }
            }

            $totalCount = $students->count();
            $skippedCount = $totalCount - $createdCount;

            return response()->json([
                'success' => true,
                'message' => "Clearance request(s) processed. Sent: {$createdCount}. Skipped (already pending): {$skippedCount}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send clearance requests: ' . $e->getMessage()
            ], 500);
        }
    }

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

    public function getStudentsForIntake(Request $request)
    {
        try {
            $intakeId = $request->input('intake_id');
            $courseId = $request->input('course_id');
            $location = $request->input('location');
            $specialization = $this->normalizeSpecializationValue($request->input('specialization'));

            $query = CourseRegistration::where('intake_id', $intakeId)
                ->whereHas('student', function ($q) {
                    $q->where('academic_status', 'active');
                })
                ->when($courseId, fn($q) => $q->where('course_id', $courseId))
                ->when($location, fn($q) => $q->where('location', $location))
                ->when($specialization, function ($q) use ($specialization) {
                    $q->where(function ($inner) use ($specialization) {
                        $inner->where('specialization', $specialization)
                            ->orWhereHas('student.semesterRegistrations', function ($semesterQuery) use ($specialization) {
                                $semesterQuery->where('specialization', $specialization);
                            })
                            ->orWhereHas('student.moduleManagements', function ($moduleQuery) use ($specialization) {
                                $moduleQuery->where('specialization', $specialization);
                            });
                    });
                })
                ->with('student');

            $registrations = $query->get();

            $students = $registrations
                ->unique('student_id')
                ->map(function ($registration) use ($intakeId, $courseId, $location, $specialization) {
                    $student = $registration->student;
                    if (!$student) {
                        return null;
                    }

                    $latestRequestQuery = ClearanceRequest::where('student_id', $student->student_id)
                        ->where('intake_id', $intakeId)
                        ->when($courseId, fn($q) => $q->where('course_id', $courseId))
                        ->when($location, fn($q) => $q->where('location', $location))
                        ->when($specialization, fn($q) => $q->where('specialization', $specialization));

                    $latest = $latestRequestQuery->orderByDesc('requested_at')->first();
                    $statusText = $latest->status_text ?? ($latest->status ?? 'No Request');

                    return [
                        'student_id' => $student->student_id,
                        'name' => $student->name_with_initials ?? $student->name ?? ($student->full_name ?? ''),
                        'specialization' => $specialization,
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
            'intake_id' => 'required|exists:intakes,intake_id',
            'course_id' => 'required|exists:courses,course_id',
            'location' => 'required|string',
            'clearance_type' => 'required|string',
            'specialization' => 'nullable|string',
        ]);

        try {
            $specialization = $this->normalizeSpecializationValue($request->specialization);

            $clearanceRequests = ClearanceRequest::where('intake_id', $request->intake_id)
                ->where('course_id', $request->course_id)
                ->where('location', $request->location)
                ->where('clearance_type', $request->clearance_type)
                ->when($specialization, fn($query) => $query->where('specialization', $specialization))
                ->with(['student', 'course', 'intake', 'approvedBy'])
                ->orderBy('requested_at', 'desc')
                ->get();

            if ($clearanceRequests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No clearance requests found for the specified criteria.'
                ]);
            }

            $firstRequest = $clearanceRequests->first();
            $intakeName = $firstRequest->intake->batch;
            $courseName = $firstRequest->course->course_name;

            $students = $clearanceRequests->map(function ($item) {
                return [
                    'student_id' => $item->student->student_id,
                    'student_name' => $item->student->name_with_initials,
                    'status' => $item->status,
                    'status_text' => $item->status_text,
                    'status_color' => $item->status_color,
                    'processed_by' => $item->approvedBy->name ?? null,
                    'processed_date' => $item->approved_at ? $item->approved_at->format('d/m/Y H:i') : null,
                    'remarks' => $item->remarks,
                    'specialization' => $item->specialization,
                ];
            });

            return response()->json([
                'success' => true,
                'intake_name' => $intakeName,
                'course_name' => $courseName,
                'specialization' => $specialization,
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
