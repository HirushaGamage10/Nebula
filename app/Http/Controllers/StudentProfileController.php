<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentGuardian;
use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\Module;
use App\Models\ExamResult;
use App\Models\StudentExam;
use App\Models\Attendance;
use App\Models\StudentClearance;
use App\Models\StudentOtherInformation;
use App\Models\Intake;
use App\Models\Batch;
use Illuminate\Support\Facades\Log;

class StudentProfileController extends Controller
{
    // Show the student profile view
    public function showStudentProfile(Request $request, $studentId)
    {
        if ($studentId === 'me') {
            $user = auth()->user();
            if (!$user || !$user->student_id) {
                return redirect()->route('dashboard')->with('error', 'No student profile associated with your account.');
            }
            $studentId = $user->student_id;
        }
        // If studentId is 0, show only the search UI (no student loaded)
        if ($studentId == 0) {
            return view('student_profile');
        }
        $student = Student::with('parentGuardian')->find($studentId);
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }
        $student->parent = $student->parentGuardian; // Attach parent relationship for Blade and JS
        Log::info('Student profile parent (Blade):', ['parent' => $student->parent]);
        return view('student_profile', compact('student'));
    }

    // Get student details (AJAX)
    public function getStudentDetails(Request $request)
    {
        $identificationType = $request->input('identificationType');
        $idValue = $request->input('idValue');
        if (empty($idValue)) {
            return response()->json(['success' => false, 'message' => 'ID value is required'], 400);
        }
        // Complete fields array with correct column names
        $fields = [
            'student_id', // Always include this for relationships
            'registration_id',
            'full_name',
            'name_with_initials',
            'title',
            'gender',
            'birthday', // Correct column name
            'id_value', // Correct column name
            'id_type',
            'email',
            'mobile_phone',
            'home_phone',
            'emergency_contact_number',
            'address',
            'foundation_program',
            'special_needs',
            'extracurricular_activities',
            'future_potentials',
            'institute_location',
            'course_id',
            'intake',
            'status',
            'remarks'
        ];
        $student = null;
        switch ($identificationType) {
            case 'registration_number':
                $student = Student::with('parentGuardian')->select($fields)->where('registration_id', $idValue)->first();
                break;
            case 'id_number':
                $student = Student::with('parentGuardian')->select($fields)->where('id_number', $idValue)->first();
                break;
            case 'Course_registration_id':
                $courseRegistration = CourseRegistration::where('id', $idValue)->first();
                if ($courseRegistration) {
                    $student = Student::with('parentGuardian')->select($fields)->where('student_id', $courseRegistration->student_id)->first();
                }
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid identification type'], 400);
        }
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        $student->parent = $student->parentGuardian;
        Log::info('Student profile parent (AJAX):', ['parent' => $student->parent]);
        // Enhance student data (no payment details)
        $student->course_registrations = CourseRegistration::where('student_id', $student->student_id)->get();
        $student->exams = StudentExam::where('student_id', $student->student_id)->get();
        $student->attendance = Attendance::where('student_id', $student->student_id)->get();
        $student->exam_results = ExamResult::where('student_id', $student->student_id)->get();
        $student->other_information = StudentOtherInformation::where('student_id', $student->student_id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Student found',
            'student' => $student
        ]);
    }

    // Update personal info
    public function updatePersonalInfo(Request $request, $studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }
        $validated = $request->validate([
            'name_with_initials' => 'required|string|max:255',
            'birthday' => 'required|date',
            'email' => 'required|email',
            'mobile_phone' => 'required|string',
            'address' => 'required|string',
        ]);
        $student->name_with_initials = $validated['name_with_initials'];
        $student->birthday = $validated['birthday'];
        $student->email = $validated['email'];
        $student->mobile_phone = $validated['mobile_phone'];
        $student->address = $validated['address'];
        if ($student->save()) {
            return response()->json(['success' => true, 'student' => $student]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update.']);
        }
    }

    // Update personal info via AJAX
    public function updatePersonalInfoAjax(Request $request)
    {
        $studentId = $request->input('student_id');
        $student = Student::find($studentId);
        
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }

        try {
            // Update all personal info fields
            $student->title = $request->input('title');
            $student->full_name = $request->input('full_name');
            $student->id_value = $request->input('id_value');
            $student->registration_id = $request->input('registration_id');
            $student->institute_location = $request->input('institute_location');
            $student->birthday = $request->input('birthday');
            $student->gender = $request->input('gender');
            $student->email = $request->input('email');
            $student->mobile_phone = $request->input('mobile_phone');
            $student->home_phone = $request->input('home_phone');
            $student->emergency_contact_number = $request->input('emergency_contact_number');
            $student->address = $request->input('address');
            $student->foundation_program = $request->input('foundation_program');
            $student->special_needs = $request->input('special_needs');
            $student->extracurricular_activities = $request->input('extracurricular_activities');
            $student->future_potentials = $request->input('future_potentials');

            $student->save();

            return response()->json([
                'success' => true, 
                'message' => 'Personal information updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update personal information: ' . $e->getMessage()
            ]);
        }
    }

    // Update parent/guardian info via AJAX
    public function updateParentInfoAjax(Request $request)
    {
        $studentId = $request->input('student_id');
        $student = Student::find($studentId);
        
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }

        try {
            // Find or create parent/guardian record
            $parentGuardian = ParentGuardian::where('student_id', $studentId)->first();
            
            if (!$parentGuardian) {
                $parentGuardian = new ParentGuardian();
                $parentGuardian->student_id = $studentId;
            }

            // Update parent/guardian fields
            $parentGuardian->guardian_name = $request->input('guardian_name');
            $parentGuardian->guardian_profession = $request->input('guardian_profession');
            $parentGuardian->guardian_contact_number = $request->input('guardian_contact_number');
            $parentGuardian->guardian_email = $request->input('guardian_email');
            $parentGuardian->guardian_address = $request->input('guardian_address');
            $parentGuardian->emergency_contact_number = $request->input('emergency_contact_number');

            $parentGuardian->save();

            return response()->json([
                'success' => true, 
                'message' => 'Parent/Guardian information updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update parent/guardian information: ' . $e->getMessage()
            ]);
        }
    }

    // API: Get course registration history for a student
    public function getCourseRegistrationHistory($studentId)
    {
        try {
            $registrations = CourseRegistration::where('student_id', $studentId)
                ->with(['course', 'intake'])
                ->orderBy('created_at', 'desc')
                ->get();

            $history = $registrations->map(function($registration) {
                return [
                    'course_name' => $registration->course->course_name ?? 'N/A',
                    'intake' => $registration->intake->batch ?? 'N/A',
                    'start_date' => $registration->created_at ? $registration->created_at->format('d/m/Y') : 'N/A',
                    'end_date' => $registration->end_date ? $registration->end_date->format('d/m/Y') : 'N/A',
                    'status' => $registration->status ?? 'N/A'
                ];
            });

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch course registration history: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Get intakes for a specific course
    public function getIntakesForCourse($studentId, $courseId)
    {
        try {
            $intakes = CourseRegistration::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->with('intake')
                ->get()
                ->pluck('intake.batch')
                ->filter()
                ->unique()
                ->values();

            return response()->json([
                'success' => true,
                'intakes' => $intakes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch intakes: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Get payment details for a specific course and intake
    public function getPaymentDetails($studentId, $courseId, $intake)
    {
        try {
            // Mock payment data - replace with actual payment model queries
            $paymentData = [
                'total_fee' => '150,000 LKR',
                'paid_amount' => '75,000 LKR',
                'balance' => '75,000 LKR',
                'payment_status' => 'Partially Paid'
            ];

            return response()->json([
                'success' => true,
                'total_fee' => $paymentData['total_fee'],
                'paid_amount' => $paymentData['paid_amount'],
                'balance' => $paymentData['balance'],
                'payment_status' => $paymentData['payment_status']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment details: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Get payment history for a specific course and intake
    public function getPaymentHistory($studentId, $courseId, $intake)
    {
        try {
            // Mock payment history - replace with actual payment model queries
            $paymentHistory = [
                [
                    'payment_date' => '15/01/2025',
                    'amount' => '50,000 LKR',
                    'payment_method' => 'Bank Transfer',
                    'receipt_url' => null
                ],
                [
                    'payment_date' => '15/02/2025',
                    'amount' => '25,000 LKR',
                    'payment_method' => 'Cash',
                    'receipt_url' => '/receipts/receipt_001.pdf'
                ]
            ];

            return response()->json([
                'success' => true,
                'history' => $paymentHistory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Get payment schedule for a specific course and intake
    public function getPaymentSchedule($studentId, $courseId, $intake)
    {
        try {
            // Mock payment schedule - replace with actual payment model queries
            $paymentSchedule = [
                [
                    'due_date' => '15/01/2025',
                    'amount' => '50,000 LKR',
                    'status' => 'Paid',
                    'payment_date' => '15/01/2025',
                    'receipt_url' => null
                ],
                [
                    'due_date' => '15/02/2025',
                    'amount' => '50,000 LKR',
                    'status' => 'Paid',
                    'payment_date' => '15/02/2025',
                    'receipt_url' => '/receipts/receipt_001.pdf'
                ],
                [
                    'due_date' => '15/03/2025',
                    'amount' => '50,000 LKR',
                    'status' => 'Pending',
                    'payment_date' => null,
                    'receipt_url' => null
                ]
            ];

            return response()->json([
                'success' => true,
                'schedule' => $paymentSchedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Get student details by NIC (for AJAX search)
    public function getStudentDetailsByNic(Request $request)
    {
        $nic = $request->query('nic');
        if (!$nic) {
            return response()->json(['success' => false, 'message' => 'NIC is required.'], 400);
        }
        $student = \App\Models\Student::with('parentGuardian')->where('id_value', $nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }
        // Attach parent relationship for consistency with other methods
        $student->parent = $student->parentGuardian;
        Log::info('Student profile parent (NIC search):', ['parent' => $student->parent]);
        return response()->json(['success' => true, 'student' => $student]);
    }
    // Other methods (academic details, attendance, clearance, certificates, etc.) remain unchanged
}
