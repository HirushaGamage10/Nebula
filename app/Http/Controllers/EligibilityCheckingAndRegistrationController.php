<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\Courses;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Storage;

class EligibilityCheckingAndRegistrationController extends Controller
{



   public function index()
{
    return view('eligibility_checking_&_registration');
}

public function search(Request $request)
{
    $nic = $request->input('nic');
    $courseId = $request->input('course_id');

    $student = Students::where('nic_number', $nic)->first();
    $course = Courses::find($courseId);

    if (!$student || !$course) {
        return back()->with('error', 'No matching student or course found.');
    }

    return view('eligibility_checking_&_registration', [
        'student' => $student,
        'course' => $course,
        'showDetails' => true,
    ]);
}

    // Show the eligibility registration view
    public function showEligibilityRegistration()
    {
        return view('eligibility_registration');
    }

    // Fetch courses by location
    public function getCoursesByLocation(Request $request)
    {
        $location = $request->query('location');
        $courseType = $request->query('course_type'); // e.g., 'degree'
        $query = Course::where('location', $location);
        if ($courseType) {
            $query->where('course_type', $courseType);
        }
        $courses = $query->orderBy('course_name')->get(['course_id', 'course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    // Fetch intakes for a course and location
    public function getIntakesForCourseAndLocation($courseId, $location)
    {
        $course = \App\Models\Course::find($courseId);
        if (!$course) {
            return response()->json(['intakes' => []]);
        }
        $intakes = \App\Models\Intake::where('course_name', $course->course_name)
            ->where('location', $location)
            ->orderBy('batch')
            ->get(['intake_id', 'batch']);
        return response()->json(['intakes' => $intakes]);
    }

    // Fetch eligible students for the table
    public function getEligibleStudents(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
        ]);
        $students = CourseRegistration::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->with('student')
            ->get()
            ->map(function($reg) {
                return [
                    'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->full_name,
                    'approval_status' => $reg->approval_status,
                ];
            });
        return response()->json(['success' => true, 'students' => $students]);
    }

    // Approve a student
    public function verifyEligibility(Request $request)
    {
        $request->validate(['student_id' => 'required|integer']);
        
        $registration = CourseRegistration::where('student_id', $request->student_id)
            ->where('status', 'Special approval required')
            ->first();
            
        if ($registration) {
            $registration->approval_status = 'Approved by manager';
            $registration->status = 'Registered';
            $registration->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'Student approved successfully',
                'registration_id' => $registration->id
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'No pending special approval registration found for this student'
            ]);
        }
    }

    // Get registered courses for a student by NIC
    public function getRegisteredCoursesByNic(Request $request)
    {
        $nic = $request->query('nic');
        \Log::debug('Eligibility NIC search', ['nic' => $nic]);
        $student = \App\Models\Student::where('id_value', $nic)->first();
        \Log::debug('Found student', ['student_id' => $student->student_id, 'id_value' => $student->id_value]);
        if (!$student) {
            \Log::debug('No student found for NIC', ['nic' => $nic]);
            return response()->json(['success' => false, 'courses' => []]);
        }
        $courseRegs = \App\Models\CourseRegistration::where('student_id', $student->student_id)
            ->with('course')
            ->get();
        \Log::debug('Course registrations', ['count' => $courseRegs->count(), 'ids' => $courseRegs->pluck('course_id')]);
        $courses = $courseRegs->filter(function($reg) {
            return $reg->course !== null;
        })->map(function($reg) {
            return [
                'course_id' => $reg->course->course_id,
                'course_name' => $reg->course->course_name
            ];
        })->values();
        \Log::debug('Returned courses', ['courses' => $courses]);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    // Get eligible students for a NIC and course
    public function getEligibleStudentsByNic(Request $request)
    {
        $request->validate([
            'nic' => 'required',
            'course_id' => 'required|exists:courses,course_id',
        ]);
        $student = \App\Models\Student::where('id_value', $request->nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'students' => []]);
        }
        $reg = \App\Models\CourseRegistration::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->first();
        if (!$reg) {
            return response()->json(['success' => false, 'students' => []]);
        }
        $students = [[
            'registration_number' => $student->registration_id ?? $student->student_id,
            'student_id' => $student->student_id,
            'name' => $student->full_name,
            'approval_status' => $reg->approval_status,
        ]];
        return response()->json(['success' => true, 'students' => $students]);
    }

    public function getStudentExamDetailsByNicCourse(Request $request)
    {
        $request->validate([
            'nic' => 'required',
            'course_id' => 'required|exists:courses,course_id',
        ]);
        $student = \App\Models\Student::where('id_value', $request->nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }
        $exam = \App\Models\StudentExam::where('student_id', $student->student_id)->first();
        $ol = $al = null;
        if ($exam) {
            $ol = [
                'type' => $exam->ol_exam_type,
                'year' => $exam->ol_exam_year,
                'subjects' => $exam->ol_exam_subjects ? json_decode($exam->ol_exam_subjects, true) : [],
            ];
            $al = [
                'type' => $exam->al_exam_type,
                'year' => $exam->al_exam_year,
                'stream' => $exam->al_exam_stream,
                'subjects' => $exam->al_exam_subjects ? json_decode($exam->al_exam_subjects, true) : [],
                'remarks' => $exam->remarks ?? '',
            ];
        }
        // Fetch course registration for this student and course
        $registration = \App\Models\CourseRegistration::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->first();
        $intake_id = $registration ? $registration->intake_id : null;
        $course_registration_id = $registration ? $registration->course_registration_id : null;
        $intake_batch = null;
        if ($intake_id) {
            $intake = \App\Models\Intake::find($intake_id);
            $intake_batch = $intake ? $intake->batch : null;
        }
        return response()->json([
            'success' => true,
            'student' => [
                'student_id' => $student->student_id,
                'location' => $student->location, // Make sure this field exists in your Student model/table
                'full_name' => $student->full_name,
                'nic' => $student->id_value,
                'ol' => $ol,
                'al' => $al,
                'intake_id' => $intake_id,
                'intake_batch' => $intake_batch,
                'course_registration_id' => $course_registration_id,
            ]
        ]);
    }

    // Special Approval List endpoint
    public function getSpecialApprovalList(Request $request)
    {
        // Debug: Check if there are any special approval registrations
        $count = CourseRegistration::where('status', 'Special approval required')->count();
        \Log::info('Special approval registrations count:', ['count' => $count]);
        
        $registrations = CourseRegistration::where('status', 'Special approval required')
            ->with(['student', 'course', 'intake'])
            ->get();
            
        \Log::info('Found registrations:', ['count' => $registrations->count()]);
        
        $mappedData = $registrations->map(function($reg) {
            // Debug: Log the student data
            \Log::info('Student data for special approval:', [
                'student_id' => $reg->student->student_id,
                'id_value' => $reg->student->id_value,
                'nic_number' => $reg->student->nic_number ?? 'not set',
                'full_name' => $reg->student->full_name,
                'registration_id' => $reg->student->registration_id ?? 'not set',
            ]);
            
            // Get document URL if available
            $documentUrl = null;
            if ($reg->special_approval_pdf) {
                $documentUrl = Storage::disk('public')->url($reg->special_approval_pdf);
                // Ensure the URL has the correct protocol and domain
                if (!str_starts_with($documentUrl, 'http')) {
                    $documentUrl = request()->getScheme() . '://' . request()->getHttpHost() . '/storage/' . $reg->special_approval_pdf;
                }
            }
            
            return [
                'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                'student_id' => $reg->student->student_id,
                'registration_id' => $reg->id, // Add the actual registration ID
                'name' => $reg->student->full_name,
                'nic' => $reg->student->id_value ?? $reg->student->nic_number ?? 'N/A',
                'course_id' => $reg->course_id,
                'course_name' => $reg->course->course_name ?? 'Unknown Course',
                'intake' => $reg->intake ? $reg->intake->batch : '2025-September',
                'approval_status' => $reg->approval_status,
                'document_url' => $documentUrl,
                'document_path' => $reg->special_approval_pdf,
                'remarks' => $reg->remarks,
                'dgm_comment' => $reg->dgm_comment,
            ];
        });
        
        // Debug: Log the final response
        \Log::info('Final response data:', $mappedData->toArray());
        
        return response()->json(['success' => true, 'students' => $mappedData]);
    }

    // Register eligible student
    public function registerEligibleStudent(Request $request)
    {
        $request->validate([
            'nic' => 'required',
            'course_id' => 'required|exists:courses,course_id',
        ]);
        $student = Student::where('id_value', $request->nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }
        $course = Course::find($request->course_id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.']);
        }
        // Find or create registration
        $registration = CourseRegistration::firstOrNew([
            'student_id' => $student->student_id,
            'course_id' => $course->course_id,
        ]);
        // Assign intake_id from existing registration or from request (if available)
        $intake_id = $registration->intake_id ?? $request->input('intake_id');
        if (!$intake_id) {
            // Try to get intake from latest registration for this course
            $latestReg = CourseRegistration::where('course_id', $course->course_id)
                ->orderByDesc('id')->first();
            $intake_id = $latestReg ? $latestReg->intake_id : null;
        }
        $registration->intake_id = $intake_id;
        // Assign course_registration_id using intake pattern
        if ($intake_id) {
            $intake = \App\Models\Intake::find($intake_id);
            if ($intake && $intake->course_registration_id_pattern) {
                $pattern = $intake->course_registration_id_pattern;
                if (preg_match('/^(.*?)(\d+)$/', $pattern, $matches)) {
                    $prefix = $matches[1];
                    $numberLength = strlen($matches[2]);
                    $latest = CourseRegistration::where('intake_id', $intake_id)
                        ->where('course_registration_id', 'like', $prefix . '%')
                        ->orderByDesc('course_registration_id')
                        ->first();
                    if ($latest && preg_match('/^(.*?)(\d+)$/', $latest->course_registration_id, $latestMatches)) {
                        $nextNumber = str_pad(((int)$latestMatches[2]) + 1, $numberLength, '0', STR_PAD_LEFT);
                    } else {
                        $nextNumber = $matches[2];
                    }
                    $registration->course_registration_id = $prefix . $nextNumber;
                }
            }
        }
        $registration->status = 'Registered';
        $registration->approval_status = 'Approved by manager';
        $registration->registration_date = now();
        $registration->location = $course->location;
        $registration->registration_fee = $course->registration_fee ?? 0;
        $registration->remarks = 'Registered via eligibility page';
        $registration->save();
        return response()->json([
            'success' => true, 
            'message' => 'Student registered successfully.',
            'course_registration_id' => $registration->course_registration_id
        ]);
    }

    // Get course entry qualification
    public function getCourseEntryQualification(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
        ]);
        $course = Course::find($request->course_id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.']);
        }
        return response()->json([
            'success' => true,
            'course' => [
                'course_name' => $course->course_name,
                'entry_qualification' => $course->entry_qualification,
            ]
        ]);
    }

    // Get course details
    public function getCourseDetails($courseId)
    {
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.']);
        }
        
        // Get the latest intake for this course
        $latestIntake = \App\Models\Intake::where('course_name', $course->course_name)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $intake = $latestIntake ? $latestIntake->batch : '2025-September';
        
        return response()->json([
            'success' => true,
            'course' => [
                'course_id' => $course->course_id,
                'course_name' => $course->course_name,
                'location' => $course->location,
                'course_type' => $course->course_type,
                'intake' => $intake,
            ]
        ]);
    }



    // Send special approval request to DGM
    public function sendSpecialApprovalRequest(Request $request)
    {
        // Debug logging
        \Log::info('Special approval request received', [
            'request_data' => $request->all(),
            'files' => $request->allFiles(),
            'user' => auth()->user() ? auth()->user()->user_role : 'not authenticated'
        ]);
        
        $request->validate([
            'nic' => 'required',
            'course_id' => 'required|exists:courses,course_id',
            'special_approval_document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            'remarks' => 'nullable|string|max:1000',
        ]);
        
        $student = \App\Models\Student::where('id_value', $request->nic)->first();
        $course = \App\Models\Course::find($request->course_id);
        
        if (!$student || !$course) {
            return response()->json(['success' => false, 'message' => 'Student or course not found.']);
        }
        
        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('special_approval_document')) {
            $file = $request->file('special_approval_document');
            $documentPath = $file->store('special_approvals', 'public');
        }
        
        // Find or create registration with "Special approval required" status
        $registration = CourseRegistration::firstOrNew([
            'student_id' => $student->student_id,
            'course_id' => $course->course_id,
        ]);
        
        $registration->status = 'Special approval required';
        $registration->approval_status = 'Pending';
        $registration->registration_date = now();
        $registration->location = $course->location;
        $registration->registration_fee = $course->registration_fee ?? 0;
        $registration->special_approval_pdf = $documentPath;
        $registration->remarks = $request->input('remarks', 'Special approval requested via eligibility page');
        $registration->save();
        
        // Find DGM user(s) and log the request
        $dgms = \App\Models\User::where('user_role', 'DGM')->get();
        if ($dgms->isEmpty()) {
            \Log::warning('No DGM user found for special approval request', [
                'student_id' => $student->student_id,
                'course_id' => $course->course_id
            ]);
        } else {
            foreach ($dgms as $dgm) {
                \Log::info('Special approval request sent to DGM', [
                    'dgm_id' => $dgm->user_id,
                    'dgm_name' => $dgm->name,
                    'student_id' => $student->student_id,
                    'student_name' => $student->full_name,
                    'course_id' => $course->course_id,
                    'course_name' => $course->course_name,
                    'registration_id' => $registration->id,
                    'document_path' => $documentPath
                ]);
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Special approval request with document uploaded successfully.',
            'registration_id' => $registration->id
        ]);
    }

    // Get next available course registration ID for an intake
    public function getNextCourseRegistrationId(Request $request)
    {
        // Accept both GET and POST
        $intake_id = $request->input('intake_id');
        if (!$intake_id || !is_numeric($intake_id)) {
            return response()->json(['success' => false, 'message' => 'Intake ID is required and must be numeric.']);
        }
        $intake = \App\Models\Intake::find($intake_id);
        if (!$intake) {
            return response()->json(['success' => false, 'message' => 'Intake not found.']);
        }
        $pattern = $intake->course_registration_id_pattern;
        if (!$pattern) {
            return response()->json(['success' => false, 'message' => 'No pattern set for this intake.']);
        }
        // Extract prefix and numeric part from pattern
        if (preg_match('/^(.*?)(\d+)$/', $pattern, $matches)) {
            $prefix = $matches[1];
            $numberLength = strlen($matches[2]);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid pattern format.']);
        }
        // Find the latest registration ID for this intake that matches the prefix
        $latest = \App\Models\CourseRegistration::where('intake_id', $intake->intake_id)
            ->where('course_registration_id', 'like', $prefix . '%')
            ->orderByDesc('course_registration_id')
            ->first();
        if ($latest && preg_match('/^(.*?)(\d+)$/', $latest->course_registration_id, $latestMatches)) {
            $nextNumber = str_pad(((int)$latestMatches[2]) + 1, $numberLength, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = $matches[2];
        }
        $nextId = $prefix . $nextNumber;
        return response()->json(['success' => true, 'next_id' => $nextId]);
    }

    // Update DGM comment for a special approval request
    public function updateDgmComment(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:course_registration,id',
            'dgm_comment' => 'nullable|string|max:1000',
        ]);

        $registration = CourseRegistration::find($request->registration_id);
        
        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found.']);
        }

        if ($registration->status !== 'Special approval required') {
            return response()->json(['success' => false, 'message' => 'This registration is not pending special approval.']);
        }

        $registration->dgm_comment = $request->input('dgm_comment');
        $registration->save();

        return response()->json([
            'success' => true, 
            'message' => 'DGM comment updated successfully.',
            'dgm_comment' => $registration->dgm_comment
        ]);
    }
}
