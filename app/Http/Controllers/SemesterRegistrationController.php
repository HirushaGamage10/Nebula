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
        \Log::info('Semester registration store method called', $request->all());

        $request->validate([
            'course_id'      => 'required|exists:courses,course_id',
            'intake_id'      => 'required|exists:intakes,intake_id',
            'semester_id'    => 'required|exists:semesters,id',
            'location'       => 'required|string',
            'specialization' => 'nullable|string|max:255',
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
                return response()->json(['success' => false, 'message' => 'No students selected for registration.'], 400);
            }

            // Validate payload entries
            foreach ($selectedStudents as $entry) {
                if (!isset($entry['student_id']) || !isset($entry['status'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid student entry format.'], 400);
                }
            }

            // Validate student IDs exist
            $studentIds       = array_column($selectedStudents, 'student_id');
            $validStudentIds  = Student::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
            $invalidStudentIds = array_diff($studentIds, $validStudentIds);
            if (!empty($invalidStudentIds)) {
                return response()->json(['success' => false, 'message' => 'Some selected students do not exist in the system.'], 400);
            }

            // SA maps
            $saReasons = $request->input('sa_reasons', []);
            $saFiles   = $request->file('sa_files', []);

            $messages = [];
            $successfullyRegisteredStudents = [];

            foreach ($selectedStudents as $entry) {
                $studentId = (int) $entry['student_id'];
                $newStatus = $entry['status']; // 'registered' | 'terminated'

                // Current semester registration (if any) for this student/intake/semester
                $current = SemesterRegistration::where('student_id', $studentId)
                    ->where('intake_id', $request->intake_id)
                    ->where('semester_id', $request->semester_id)
                    ->latest('id')
                    ->first();

                $wasTerminated = $current?->status === 'terminated';

                // === CASE A: Trying to re-register a TERMINATED student → file Special Approval (pending) ===
                if ($wasTerminated && $newStatus === 'registered') {
                    // Reason required
                    $reason = $saReasons[$studentId] ?? null;
                    if (!$reason) {
                        $messages[] = "Student {$studentId}: missing special-approval reason (kept terminated).";
                        // Do NOT update record if reason not provided
                        continue;
                    }

                    $filePath = null;
                    if (isset($saFiles[$studentId]) && $saFiles[$studentId]->isValid()) {
                        $filePath = $saFiles[$studentId]->store('semester_special_approvals', 'public');
                    }

                    // Keep status TERMINATED; set approval fields
                    SemesterRegistration::updateOrCreate(
                        [
                            'student_id'  => $studentId,
                            'intake_id'   => $request->intake_id,
                            'semester_id' => $request->semester_id,
                        ],
                        [
                            'course_id'      => $request->course_id,
                            'location'       => $request->location,
                            'specialization' => $request->specialization,

                            // status stays terminated until DGM approves
                            'status'            => 'terminated',
                            'desired_status'    => 'registered',
                            'approval_status'   => 'pending',
                            'approval_reason'   => $reason,
                            'approval_file_path' => $filePath,
                            'approval_requested_at' => now(),

                            'registration_date' => $current?->registration_date ?? now()->toDateString(),
                            'updated_at'        => now(),
                        ]
                    );

                    $messages[] = "Student {$studentId}: Special approval requested (pending DGM).";
                    continue;
                }

                // === CASE B: If there is an already-approved SA to move to registered, allow it ===
                $approvedToRegistered = $current
                    && $current->approval_status === 'approved'
                    && $current->desired_status === 'registered'
                    && $newStatus === 'registered';

                // === CASE C: Normal update (no SA needed) ===
                SemesterRegistration::updateOrCreate(
                    [
                        'student_id'  => $studentId,
                        'intake_id'   => $request->intake_id,
                        'semester_id' => $request->semester_id,
                    ],
                    [
                        'course_id'      => $request->course_id,
                        'location'       => $request->location,
                        'specialization' => $request->specialization,

                        'status'         => $approvedToRegistered ? 'registered' : $newStatus,

                        // If not in approval flow, clear flags
                        'desired_status'  => $approvedToRegistered ? null : null,
                        'approval_status' => $approvedToRegistered ? 'none' : 'none',
                        'registration_date' => now()->toDateString(),
                        'updated_at'        => now(),
                    ]
                );

                // If student is being registered (not terminated), automatically register for core and special unit compulsory modules
                if (($approvedToRegistered || $newStatus === 'registered') && !$wasTerminated) {
                    $successfullyRegisteredStudents[] = $studentId;
                }
            }

            // Automatically register students for core and special unit compulsory modules
            if (!empty($successfullyRegisteredStudents)) {
                $this->registerStudentsForCompulsoryModules(
                    $successfullyRegisteredStudents,
                    $request->course_id,
                    $request->intake_id,
                    $request->semester_id,
                    $request->location,
                    $request->specialization
                );
            }

            $note = empty($messages) ? '' : (' ' . implode(' ', $messages));
            return response()->json(['success' => true, 'message' => 'Student registration statuses processed.' . $note]);
        } catch (\Throwable $e) {
            \Log::error('Error saving semester registrations: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Server error occurred.'], 500);
        }
    }

    /**
     * Automatically register students for core and special unit compulsory modules
     */
    private function registerStudentsForCompulsoryModules($studentIds, $courseId, $intakeId, $semesterId, $location, $specialization = null)
    {
        try {
            // Get the semester to get its name
            $semester = Semester::find($semesterId);
            if (!$semester) {
                \Log::error('Semester not found for ID: ' . $semesterId);
                return;
            }

            // Get all core and special unit compulsory modules for this semester
            $compulsoryModules = \DB::table('semester_module')
                ->join('modules', 'semester_module.module_id', '=', 'modules.module_id')
                ->where('semester_module.semester_id', $semesterId)
                ->whereIn('modules.module_type', ['core', 'special_unit_compulsory'])
                ->select('semester_module.module_id', 'semester_module.specialization', 'modules.module_name')
                ->get();

            if ($compulsoryModules->isEmpty()) {
                \Log::info('No compulsory modules found for semester: ' . $semester->name);
                return;
            }

            $registeredModules = [];
            $errors = [];

            foreach ($studentIds as $studentId) {
                foreach ($compulsoryModules as $module) {
                    // Check if student is already registered for this module in this semester
                    $existingRegistration = \App\Models\ModuleManagement::where('student_id', $studentId)
                        ->where('module_id', $module->module_id)
                        ->where('semester', $semester->name)
                        ->where('course_id', $courseId)
                        ->where('intake_id', $intakeId)
                        ->where('location', $location)
                        ->first();

                    if (!$existingRegistration) {
                        try {
                            \App\Models\ModuleManagement::create([
                                'student_id' => $studentId,
                                'module_id' => $module->module_id,
                                'semester' => $semester->name,
                                'course_id' => $courseId,
                                'intake_id' => $intakeId,
                                'location' => $location,
                                'specialization' => $module->specialization ?? $specialization ?? 'General',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            $registeredModules[] = [
                                'student_id' => $studentId,
                                'module_id' => $module->module_id,
                                'module_name' => $module->module_name
                            ];

                            \Log::info("Student {$studentId} automatically registered for module: {$module->module_name}");
                        } catch (\Exception $e) {
                            $errors[] = "Failed to register student {$studentId} for module {$module->module_name}: " . $e->getMessage();
                            \Log::error("Module registration error for student {$studentId}, module {$module->module_id}: " . $e->getMessage());
                        }
                    }
                }
            }

            if (!empty($registeredModules)) {
                \Log::info('Automatic module registration completed', [
                    'total_registrations' => count($registeredModules),
                    'registrations' => $registeredModules
                ]);
            }

            if (!empty($errors)) {
                \Log::error('Errors during automatic module registration', ['errors' => $errors]);
            }

        } catch (\Exception $e) {
            \Log::error('Error in registerStudentsForCompulsoryModules: ' . $e->getMessage(), [
                'student_ids' => $studentIds,
                'course_id' => $courseId,
                'semester_id' => $semesterId,
                'trace' => $e->getTraceAsString()
            ]);
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

    // 6. Get compulsory modules for a semester (core and special unit compulsory)
    public function getCompulsoryModulesForSemester(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            $semester = Semester::find($request->semester_id);
            if (!$semester) {
                return response()->json(['success' => false, 'message' => 'Semester not found.'], 404);
            }

            // Get all core and special unit compulsory modules for this semester
            $compulsoryModules = \DB::table('semester_module')
                ->join('modules', 'semester_module.module_id', '=', 'modules.module_id')
                ->where('semester_module.semester_id', $request->semester_id)
                ->whereIn('modules.module_type', ['core', 'special_unit_compulsory'])
                ->select(
                    'semester_module.module_id',
                    'semester_module.specialization',
                    'modules.module_name',
                    'modules.module_code',
                    'modules.module_type',
                    'modules.credits'
                )
                ->orderBy('modules.module_type')
                ->orderBy('modules.module_name')
                ->get();

            $modulesByType = [
                'core' => [],
                'special_unit_compulsory' => []
            ];

            foreach ($compulsoryModules as $module) {
                $modulesByType[$module->module_type][] = [
                    'module_id' => $module->module_id,
                    'module_name' => $module->module_name,
                    'module_code' => $module->module_code,
                    'module_type' => $module->module_type,
                    'credits' => $module->credits,
                    'specialization' => $module->specialization ?? 'General'
                ];
            }

            return response()->json([
                'success' => true,
                'semester_name' => $semester->name,
                'total_modules' => $compulsoryModules->count(),
                'modules' => $modulesByType,
                'message' => $compulsoryModules->count() > 0 
                    ? "Found {$compulsoryModules->count()} compulsory modules for {$semester->name}"
                    : "No compulsory modules found for {$semester->name}"
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting compulsory modules: ' . $e->getMessage(), [
                'semester_id' => $request->semester_id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error retrieving compulsory modules.'], 500);
        }
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

        // Automatically register the student for core and special unit compulsory modules
        $this->registerStudentsForCompulsoryModules(
            [$reg->student_id],
            $reg->course_id,
            $reg->intake_id,
            $reg->semester_id,
            $reg->location,
            $reg->specialization
        );

        return response()->json(['success' => true, 'message' => 'Request approved and student registered with automatic module registration.']);
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
