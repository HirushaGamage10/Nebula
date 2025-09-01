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


class SemesterRegistrationController extends Controller
{
    public function index()
    {
        $courses   = Course::all();
        $intakes   = Intake::all();
        $semesters = Semester::all();
        return view('semester_registration', compact('courses', 'intakes', 'semesters'));
    }

    /**
     * Store semester registrations with Special-Approval support.
     *
     * Expects:
     * - register_students: JSON array [{student_id, status}]
     * - sa_reasons[<student_id>]: string (optional, required when re-registering a terminated student)
     * - sa_files[<student_id>]: uploaded file (optional)
     */
    public function store(Request $request)
    {
        Log::info('Semester registration store method called', $request->all());

        // Enhanced validation: Explicitly allow 'holding'
        $request->validate([
            'course_id'         => 'required|exists:courses,course_id',
            'intake_id'         => 'required|exists:intakes,intake_id',
            'semester_id'       => 'required|exists:semesters,id',
            'location'          => 'required|string',
            'specialization'    => 'nullable|string|max:255',
            'register_students' => 'required|string',

            // special approval maps (optional)
            'sa_reasons'   => 'array',
            'sa_reasons.*' => 'string',
            'sa_files'     => 'array',
            'sa_files.*'   => 'file|max:4096', // 4MB per file cap
        ]);

        try {
            $selectedStudents = json_decode($request->input('register_students'), true);

            if (!is_array($selectedStudents) || empty($selectedStudents)) {
                Log::warning('No students selected for registration.');
                return response()->json([
                    'success' => false,
                    'message' => 'No students selected for registration.'
                ], 400);
            }

            // Validate payload entries and allowed statuses
            foreach ($selectedStudents as $entry) {
                if (!isset($entry['student_id']) || !isset($entry['status'])) {
                    Log::warning('Invalid student entry format.', $entry);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid student entry format.'
                    ], 400);
                }
                // Ensure status is one of the allowed values
                $allowedStatuses = ['registered', 'holding', 'terminated'];
                if (!in_array(strtolower($entry['status']), $allowedStatuses)) {
                    Log::warning('Invalid status provided.', ['status' => $entry['status']]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid status. Allowed: registered, holding, terminated.'
                    ], 400);
                }
            }

            // Validate student IDs exist
            $studentIds        = array_column($selectedStudents, 'student_id');
            $validStudentIds   = Student::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
            $invalidStudentIds = array_diff($studentIds, $validStudentIds);
            if (!empty($invalidStudentIds)) {
                Log::warning('Invalid student IDs.', ['invalid' => $invalidStudentIds]);
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected students do not exist in the system.'
                ], 400);
            }

            // SA maps
            $saReasons = $request->input('sa_reasons', []);
            $saFiles   = $request->file('sa_files', []);

            $messages = [];

            // Use DB transaction for atomicity
            DB::transaction(function () use ($selectedStudents, $request, $saReasons, $saFiles, &$messages) {
                foreach ($selectedStudents as $entry) {
                    $studentId    = (int) $entry['student_id'];
                    $newStatus    = strtolower($entry['status']); // 'registered' | 'holding' | 'terminated'
                    $origFromUI   = strtolower($entry['original_status'] ?? '');

                    // Current semester registration (if any) for this student/intake/semester
                    $current = SemesterRegistration::where('student_id', $studentId)
                        ->where('intake_id',  $request->intake_id)
                        ->where('semester_id', $request->semester_id)
                        ->latest('id')
                        ->first();

                    // Consider terminated if DB says so; fall back to UI-provided original status
                    $wasTerminated = $current
                        ? ($current->status === 'terminated')
                        : ($origFromUI === 'terminated');

                    // Special-approval payload
                    $reason = $saReasons[$studentId] ?? null;
                    $hasSA  = $reason && trim($reason) !== '';

                    // === CASE A: re-registering a TERMINATED student → create/refresh a PENDING approval ===
                    if ($newStatus === 'registered' && ($wasTerminated || $hasSA)) {
                        $filePath = null;
                        if (isset($saFiles[$studentId]) && $saFiles[$studentId]->isValid()) {
                            try {
                                $filePath = $saFiles[$studentId]->store('semester_special_approvals', 'public');
                            } catch (\Exception $e) {
                                Log::error('File upload failed for student ' . $studentId, ['error' => $e->getMessage()]);
                                throw new \Exception('File upload failed for student ' . $studentId);
                            }
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

                                // keep status TERMINATED until DGM approves
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
                        continue; // IMPORTANT: do not fall through and overwrite approval_* fields
                    }

                    // === CASE B: There is an already-approved SA to move to registered → finalize ===
                    $approvedToRegistered = $current
                        && $current->approval_status === 'approved'
                        && $current->desired_status  === 'registered'
                        && $newStatus === 'registered';

                    // === CASE C: Normal update (no SA involved) ===
                    $update = [
                        'course_id'         => $request->course_id,
                        'location'          => $request->location,
                        'specialization'    => $request->specialization,
                        'status'            => $approvedToRegistered ? 'registered' : $newStatus, // Handles 'holding' here
                        'registration_date' => now()->toDateString(),
                        'updated_at'        => now(),
                    ];

                    // Only clear approval flags if we actually consumed an approved request
                    if ($approvedToRegistered) {
                        $update['desired_status']         = null;
                        $update['approval_status']        = 'none';
                        $update['approval_reason']        = null;
                        $update['approval_file_path']     = null;
                        $update['approval_requested_at']  = null;
                        $update['approval_decided_at']    = now();
                        $update['approval_decided_by']    = auth()->id();
                        // $update['approval_dgm_comment'] = $request->input('comment'); // set if you pass one
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
            Log::error('Validation error in semester registration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error saving semester registrations: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred: ' . $e->getMessage() // More specific for debugging
            ], 500);
        }
    }

    // 1. Get courses by location (degree programs only)
    public function getCoursesByLocation(Request $request)
    {
        $location = $request->input('location');
        $courses = Course::where('location', $location)
            ->where('course_type', 'degree')
            ->get(['course_id', 'course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    // 2. Get ongoing intakes for a course/location
    public function getOngoingIntakes(Request $request)
    {
        $courseId = $request->input('course_id');
        $location = $request->input('location');
        $now = now();

        \Log::info('getOngoingIntakes called', compact('courseId', 'location', 'now'));

        $activeIntakes = Intake::where('course_name', function ($q) use ($courseId) {
            $q->select('course_name')->from('courses')->where('course_id', $courseId)->limit(1);
        })
            ->where('location', $location)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get(['intake_id', 'batch']);

        $intakesWithSemesters = Intake::where('course_name', function ($q) use ($courseId) {
            $q->select('course_name')->from('courses')->where('course_id', $courseId)->limit(1);
        })
            ->where('location', $location)
            ->whereIn('intake_id', function ($q) use ($courseId) {
                $q->select('intake_id')->from('semesters')->where('course_id', $courseId);
            })
            ->get(['intake_id', 'batch']);

        $allIntakes = $activeIntakes->merge($intakesWithSemesters)->unique('intake_id');

        return response()->json(['success' => true, 'intakes' => $allIntakes]);
    }

    // 3. Get open semesters for a course/intake/location
    public function getOpenSemesters(Request $request)
    {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');

        $semesters = Semester::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->get(['id', 'name', 'status'])
            ->map(function ($semester) {
                return [
                    'semester_id'   => $semester->id,
                    'semester_name' => $semester->name,
                    'status'        => $semester->status
                ];
            });

        return response()->json(['success' => true, 'semesters' => $semesters]);
    }

    // 4. Get eligible students for a course/intake (registered from eligibility page)
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
            ->map(function ($reg) {
                $semReg = SemesterRegistration::where('student_id', $reg->student->student_id)
                    ->where('intake_id', $reg->intake_id)
                    ->latest()
                    ->first();

                return [
                    'student_id' => $reg->student->student_id,
                    'name'       => $reg->student->name_with_initials,
                    'email'      => $reg->student->email,
                    'nic'        => $reg->student->id_value,
                    'status'     => $semReg?->status ?? 'pending',
                ];
            });

        return response()->json(['success' => true, 'students' => $students]);
    }

    // 5. Get all possible semesters for a course (for semester creation page)
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

    // (Legacy small endpoint) Update a single student's status
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

    // =========================
    // DGM Actions (Approve/Reject)
    // =========================

    public function approveReRegister(Request $request)
    {
        $request->validate([
            'request_id'  => 'nullable|integer',
            'student_id'  => 'nullable|integer',
            'intake_id'   => 'nullable|integer',
            'semester_id' => 'nullable|integer',
            'comment'     => 'nullable|string'
        ]);

        // Find the record either by request_id or by (student,intake,semester)
        $reg = null;
        if ($request->filled('request_id')) {
            $reg = SemesterRegistration::find($request->request_id);
        } else {
            $reg = SemesterRegistration::where('student_id', $request->student_id)
                ->where('intake_id', $request->intake_id)
                ->where('semester_id', $request->semester_id)
                ->first();
        }

        if (!$reg || $reg->approval_status !== 'pending' || $reg->desired_status !== 'registered') {
            return response()->json(['success' => false, 'message' => 'No pending special-approval request found.'], 404);
        }

        $reg->approval_status      = 'approved';
        $reg->approval_dgm_comment = $request->comment;
        $reg->approval_decided_at  = now();
        $reg->approval_decided_by  = auth()->id();
        $reg->status               = 'registered';
        $reg->desired_status       = null;
        $reg->save();

        return response()->json(['success' => true, 'message' => 'Request approved and student registered.']);
    }


    public function rejectReRegister(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|integer',
            'intake_id'   => 'required|integer',
            'semester_id' => 'required|integer',
            'comment'     => 'nullable|string'
        ]);

        $reg = SemesterRegistration::where('student_id', $request->student_id)
            ->where('intake_id', $request->intake_id)
            ->where('semester_id', $request->semester_id)
            ->first();

        if (!$reg || $reg->approval_status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'No pending special-approval request found.'], 404);
        }

        $reg->approval_status       = 'rejected';
        $reg->approval_dgm_comment  = $request->comment;
        $reg->approval_decided_at   = now();
        $reg->approval_decided_by   = auth()->id();
        // keep status as terminated
        $reg->desired_status        = null;
        $reg->save();

        return response()->json(['success' => true, 'message' => 'Request rejected. Student remains terminated.']);
    }

    // Return all "terminated → re-register" requests for the Special Approval tab
    public function terminatedRequests(Request $request)
    {
        // Pull semester registrations that are currently terminated,
        // asked to move to "registered", and are pending DGM approval.
        $rows = SemesterRegistration::with(['student', 'course', 'intake', 'semester'])
            ->where('status', 'terminated')
            ->where('desired_status', 'registered')
            ->where('approval_status', 'pending')
            ->orderByDesc('approval_requested_at')
            ->get();

        // Shape the payload expected by the front-end
        $requests = $rows->map(function ($r) {
            return [
                'id'             => $r->id,
                'student_id'     => $r->student_id,
                'student_name'   => optional($r->student)->name_with_initials ?? optional($r->student)->full_name ?? '',
                'course_id'      => $r->course_id,
                'course_name'    => optional($r->course)->course_name ?? '',
                'intake_id'      => $r->intake_id,
                'intake'         => optional($r->intake)->batch ?? '',
                'semester_id'    => $r->semester_id,
                'semester_name'  => optional($r->semester)->name ?? '',
                'current_status' => $r->status,                 // 'terminated'
                'desired_status' => $r->desired_status,         // 'registered'
                'reason'         => $r->approval_reason ?? '',
                'document_url'   => $r->approval_file_path
                    ? \Storage::disk('public')->url($r->approval_file_path)
                    : null,
                'requested_at'   => optional($r->approval_requested_at)->toDateTimeString(),
            ];
        });

        return response()->json([
            'success'  => true,
            'requests' => $requests,
        ]);
    }
}
