<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Semester;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\SemesterRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ClearanceRequest;

class SemesterRegistrationController extends Controller
{
    public function index()
    {
        $courses   = Course::all(); // all courses
        $intakes   = Intake::all(); // all intakes
        $semesters = Semester::all();
        return view('semester_registration', compact('courses', 'intakes', 'semesters'));
    }

    /**
     * Store semester registrations with Special-Approval support.
     */
    public function store(Request $request)
    {
        Log::info('Semester registration store method called', $request->all());

        $request->validate([
            'course_id'         => 'required|exists:courses,course_id',
            'intake_id'         => 'required|exists:intakes,intake_id',
            'semester_id'       => 'required|exists:semesters,id',
            'location'          => 'required|string',
            'specialization'    => 'nullable|string|max:255',
            'register_students' => 'required|string',
            'sa_reasons'        => 'array',
            'sa_reasons.*'      => 'string',
            'sa_files'          => 'array',
            'sa_files.*'        => 'file|max:4096',
        ]);

        try {
            $selectedStudents = json_decode($request->input('register_students'), true);

            if (!is_array($selectedStudents) || empty($selectedStudents)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students selected for registration.'
                ], 400);
            }

            $allowedStatuses = ['registered', 'holding', 'terminated'];
            foreach ($selectedStudents as $entry) {
                if (!isset($entry['student_id'], $entry['status'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid student entry format.'
                    ], 400);
                }
                if (!in_array(strtolower($entry['status']), $allowedStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid status. Allowed: registered, holding, terminated.'
                    ], 400);
                }
            }

            $studentIds        = array_column($selectedStudents, 'student_id');
            $validStudentIds   = Student::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
            $invalidStudentIds = array_diff($studentIds, $validStudentIds);
            if (!empty($invalidStudentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected students do not exist in the system.'
                ], 400);
            }

            $saReasons = $request->input('sa_reasons', []);
            $saFiles   = $request->file('sa_files', []);
            $messages  = [];

            DB::transaction(function () use ($selectedStudents, $request, $saReasons, $saFiles, &$messages) {
                foreach ($selectedStudents as $entry) {
                    $studentId = (int) $entry['student_id'];
                    $newStatus = strtolower($entry['status']);
                    $origFromUI = strtolower($entry['original_status'] ?? '');

                    $current = SemesterRegistration::where('student_id', $studentId)
                        ->where('intake_id', $request->intake_id)
                        ->where('semester_id', $request->semester_id)
                        ->latest('id')
                        ->first();

                    $wasTerminated = $current
                        ? ($current->status === 'terminated')
                        : ($origFromUI === 'terminated');

                    $reason = $saReasons[$studentId] ?? null;
                    $hasSA  = $reason && trim($reason) !== '';

                    if ($newStatus === 'registered' && ($wasTerminated || $hasSA)) {
                        $filePath = null;
                        if (isset($saFiles[$studentId]) && $saFiles[$studentId]->isValid()) {
                            $filePath = $saFiles[$studentId]->store('semester_special_approvals', 'public');
                        }

                        SemesterRegistration::updateOrCreate(
                            [
                                'student_id'  => $studentId,
                                'intake_id'   => $request->intake_id,
                                'semester_id' => $request->semester_id,
                            ],
                            [
                                'course_id'             => $request->course_id,
                                'location'              => $request->location,
                                'specialization'        => $request->specialization,
                                'status'                => 'terminated',
                                'desired_status'        => 'registered',
                                'approval_status'       => 'pending',
                                'approval_reason'       => $reason ?: '—',
                                'approval_file_path'    => $filePath,
                                'approval_requested_at' => now(),
                                'registration_date'     => $current?->registration_date ?? now()->toDateString(),
                                'updated_at'            => now(),
                            ]
                        );

                        $messages[] = "Student {$studentId}: Special approval requested (pending DGM).";
                        continue;
                    }

                    $approvedToRegistered = $current
                        && $current->approval_status === 'approved'
                        && $current->desired_status === 'registered'
                        && $newStatus === 'registered';

                    $update = [
                        'course_id'         => $request->course_id,
                        'location'          => $request->location,
                        'specialization'    => $request->specialization,
                        'status'            => $approvedToRegistered ? 'registered' : $newStatus,
                        'registration_date' => now()->toDateString(),
                        'updated_at'        => now(),
                    ];

                    if ($approvedToRegistered) {
                        $update['desired_status']        = null;
                        $update['approval_status']       = 'none';
                        $update['approval_reason']       = null;
                        $update['approval_file_path']    = null;
                        $update['approval_requested_at'] = null;
                        $update['approval_decided_at']   = now();
                        $update['approval_decided_by']   = auth()->id();
                    }

                    SemesterRegistration::updateOrCreate(
                        [
                            'student_id'  => $studentId,
                            'intake_id'   => $request->intake_id,
                            'semester_id' => $request->semester_id,
                        ],
                        $update
                    );
                }
            });

            $note = empty($messages) ? '' : (' ' . implode(' ', $messages));
            return response()->json([
                'success' => true,
                'message' => 'Student registration statuses processed.' . $note
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all courses (no location filter)
     */
    public function getCoursesByLocation(Request $request)
    {
        $courses = Course::all(['course_id', 'course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    /**
     * Get ongoing intakes for a course/location
     */
    public function getOngoingIntakes(Request $request)
    {
        $courseId = $request->input('course_id');
        $location = $request->input('location');

        $now = now();

        $activeIntakes = Intake::where('course_id', $courseId)
            ->when($location, fn($q) => $q->where('location', $location))
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get(['intake_id', 'batch']);

        return response()->json(['success' => true, 'intakes' => $activeIntakes]);
    }

    /**
     * Get open semesters for a course/intake
     */
    public function getOpenSemesters(Request $request)
    {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');

        $semesters = Semester::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->get(['id', 'name', 'status'])
            ->map(fn($semester) => [
                'semester_id'   => $semester->id,
                'semester_name' => $semester->name,
                'status'        => $semester->status
            ]);

        return response()->json(['success' => true, 'semesters' => $semesters]);
    }

    /**
     * Get eligible students
     */
    public function getEligibleStudents(Request $request)
    {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');

        $students = CourseRegistration::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->where(function ($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with('student')
            ->get()
            ->map(fn($reg) => [
                'student_id' => $reg->student->student_id,
                'name'       => $reg->student->name_with_initials,
                'email'      => $reg->student->email,
                'nic'        => $reg->student->id_value,
                'status'     => SemesterRegistration::where('student_id', $reg->student->student_id)
                                  ->where('intake_id', $reg->intake_id)
                                  ->latest()
                                  ->first()?->status ?? 'pending',
            ]);

        return response()->json(['success' => true, 'students' => $students]);
    }

    /**
     * Get all possible semesters for a course
     */
    public function getAllSemestersForCourse(Request $request)
    {
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);
        if (!$course || !$course->no_of_semesters) {
            return response()->json(['success' => false, 'semesters' => [], 'message' => 'Course not found or no semesters defined.']);
        }

        $createdSemesterNames = Semester::where('course_id', $courseId)->pluck('name')->toArray();
        $allPossibleSemesters = [];
        for ($i = 1; $i <= $course->no_of_semesters; $i++) {
            if (!in_array($i, $createdSemesterNames)) {
                $allPossibleSemesters[] = [
                    'semester_id'   => $i,
                    'semester_name' => 'Semester ' . $i
                ];
            }
        }

        return response()->json(['success' => true, 'semesters' => $allPossibleSemesters]);
    }

    /**
     * Update a single student's status
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|integer',
            'semester_id' => 'required|integer',
            'intake_id'   => 'required|integer',
            'status'      => 'required|in:registered,terminated',
        ]);

        SemesterRegistration::updateOrCreate(
            [
                'student_id'  => $request->student_id,
                'semester_id' => $request->semester_id,
            ],
            [
                'intake_id' => $request->intake_id,
                'status'    => $request->status,
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    // Remaining DGM actions (approveReRegister, rejectReRegister, terminatedRequests, checkStudentClearances)
    // can be copied from your existing code as-is, no changes needed
}
