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
use App\Models\PaymentDetail;
use App\Models\PaymentPlan;
use App\Models\StudentPaymentPlan;
use App\Models\StudentClearance;
use App\Models\StudentOtherInformation;
use App\Models\Intake;
use App\Models\Batch;
use Illuminate\Support\Facades\Log;
use App\Models\StudentStatusHistory;
use Illuminate\Support\Facades\Storage;

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
        if ($studentId == 0) {
            return view('student_profile');
        }
        $student = Student::with('parentGuardian')->find($studentId);
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }
        $student->parent = $student->parentGuardian;
        $student->other_information = StudentOtherInformation::where('student_id', $studentId)->first();
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
    $student = \App\Models\Student::find($studentId);

    if (!$student) {
        return response()->json(['success' => false, 'message' => 'Student not found.']);
    }

    try {
        // Validate required fields
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'title' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'id_value' => 'required|string|max:255',
            'institute_location' => 'required|string|max:255',
            'birthday' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'email' => 'required|email|max:255',
            'mobile_phone' => 'required|string|max:20',
            'home_phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            // Optional fields
            'name_with_initials' => 'nullable|string|max:255',
            'foundation_program' => 'nullable|boolean',
            'special_needs' => 'nullable|string',
            'extracurricular_activities' => 'nullable|string',
            'future_potentials' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string|max:20'
        ]);

        // Update student fields
        $student->title = $validatedData['title'];
        $student->full_name = $validatedData['full_name'];
        $student->id_value = $validatedData['id_value'];
        $student->institute_location = $validatedData['institute_location'];
        $student->birthday = $validatedData['birthday'];
        $student->gender = $validatedData['gender'];
        $student->email = $validatedData['email'];
        $student->mobile_phone = $validatedData['mobile_phone'];
        $student->address = $validatedData['address'];
        
        // Optional fields
        if (isset($validatedData['name_with_initials'])) {
            $student->name_with_initials = $validatedData['name_with_initials'];
        }
        if (isset($validatedData['home_phone'])) {
            $student->home_phone = $validatedData['home_phone'];
        }
        if (isset($validatedData['foundation_program'])) {
            $student->foundation_program = $validatedData['foundation_program'];
        }
        if (isset($validatedData['special_needs'])) {
            $student->special_needs = $validatedData['special_needs'];
        }
        if (isset($validatedData['extracurricular_activities'])) {
            $student->extracurricular_activities = $validatedData['extracurricular_activities'];
        }
        if (isset($validatedData['future_potentials'])) {
            $student->future_potentials = $validatedData['future_potentials'];
        }

        $student->save();

        // ✅ Handle emergency contact number (belongs to ParentGuardian)
        if (!empty($validatedData['emergency_contact_number'])) {
            \App\Models\ParentGuardian::updateOrCreate(
                ['student_id' => $studentId],
                ['emergency_contact_number' => $validatedData['emergency_contact_number']]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Personal information updated successfully!'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);
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
        $registrations = \App\Models\CourseRegistration::where('student_id', $studentId)
            ->with(['course', 'intake'])
            ->orderBy('created_at', 'desc')
            ->get();

        $history = $registrations->map(function ($registration) {
            $semesterReg = \App\Models\SemesterRegistration::where('student_id', $registration->student_id)
                ->where('course_id', $registration->course_id)
                ->where('intake_id', $registration->intake_id)
                ->first();

            return [
                'id' => $registration->id,
                'course_id' => $registration->course_id,
                'course_name' => $registration->course->course_name ?? 'N/A',
                'intake' => $registration->intake->batch ?? 'N/A',
                'status' => $registration->status ?? 'N/A',
                'full_grade' => $registration->full_grade ?? '',
                'specialization' => $semesterReg ? $semesterReg->specialization : ($registration->specialization ?? ''),
            ];
        });

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    public function getCourseSpecializations($courseId)
    {
        $course = \App\Models\Course::find($courseId);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }
        $specializations = [];
        if ($course->specializations) {
            if (is_string($course->specializations)) {
                try {
                    $specializations = json_decode($course->specializations, true);
                } catch (\Exception $e) {
                    $specializations = [];
                }
            } elseif (is_array($course->specializations)) {
                $specializations = $course->specializations;
            }
        }
        // Remove empty/null values
        $specializations = array_filter($specializations, function($s){ return $s && trim($s) !== ''; });
        return response()->json(['success' => true, 'specializations' => array_values($specializations)]);
    }

    // API: Update course registration grade and specialization
    public function updateCourseRegistrationGrade(Request $request, $id)
    {
        // Validate inputs
        $validated = $request->validate([
            'full_grade' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        $registration = \App\Models\CourseRegistration::find($id);
        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found.'], 404);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Update only the full_grade column on the course_registration table using query builder
            \Illuminate\Support\Facades\DB::table('course_registration')
                ->where('id', $registration->id)
                ->update([
                    'full_grade' => $validated['full_grade'] ?? null,
                    'updated_at' => now()
                ]);

            // Update or create the corresponding semester_registration so specialization is persisted
            $semesterReg = \App\Models\SemesterRegistration::where('student_id', $registration->student_id)
                ->where('course_id', $registration->course_id)
                ->where('intake_id', $registration->intake_id)
                ->first();

            if (!$semesterReg) {
                // Determine the semester_id for this course+intake. Prefer exact match, fall back to latest semester for the course.
                $semester = \App\Models\Semester::where('course_id', $registration->course_id)
                    ->where('intake_id', $registration->intake_id)
                    ->first();

                if (!$semester) {
                    // fallback: most recent semester for the course (if any)
                    $semester = \App\Models\Semester::where('course_id', $registration->course_id)
                        ->orderBy('start_date', 'desc')
                        ->first();
                }

                if (!$semester) {
                    // No semester found - cannot create semester_registration because semester_id is required
                    throw new \Exception('No semester found for the course/intake to persist specialization.');
                }

                // Normalize status to allowed enum values
                $allowedStatuses = ['registered', 'pending', 'cancelled'];
                $regStatus = strtolower(trim((string)($registration->status ?? '')));
                $statusToSet = in_array($regStatus, $allowedStatuses) ? $regStatus : 'registered';

                // create a minimal semester registration record with a valid semester_id
                $semesterReg = \App\Models\SemesterRegistration::create([
                    'student_id' => $registration->student_id,
                    'semester_id' => $semester->id,
                    'course_id' => $registration->course_id,
                    'intake_id' => $registration->intake_id,
                    'location' => $registration->location ?? null,
                    'status' => $statusToSet,
                    'registration_date' => $registration->created_at ?? now(),
                ]);
            }

            if (array_key_exists('specialization', $validated)) {
                // Preserve existing specialization when incoming is empty string
                $newSpec = $validated['specialization'] !== '' ? $validated['specialization'] : $semesterReg->specialization;
                $semesterReg->specialization = $newSpec;
                $semesterReg->save();
            }

            \Illuminate\Support\Facades\DB::commit();

            // Return the final stored specialization so client can update UI from authoritative source
            return response()->json([
                'success' => true,
                'specialization' => $semesterReg->specialization
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Database error updating course registration grade/specialization', ['message' => $e->getMessage(), 'registration_id' => $id]);
            return response()->json(['success' => false, 'message' => 'Failed to update registration.'], 500);
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

    public function getStudentDetailsByNic(Request $request)
    {
        $nic = $request->query('nic');
        if (!$nic) {
            return response()->json(['success' => false, 'message' => 'NIC is required.'], 400);
        }
        $student = \App\Models\Student::with(['parentGuardian', 'otherInformation'])->where('id_value', $nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }
        $student->parent = $student->parentGuardian;
        $student->other_information = $student->otherInformation; // <-- Ensure this is set
        $student->exams = \App\Models\StudentExam::where('student_id', $student->student_id)->get();
        return response()->json(['success' => true, 'student' => $student]);
    }
    // Other methods (academic details, attendance, clearance, certificates, etc.) remain unchanged




    //show exam results 
    public function getRegisteredCourses($studentId)
    {
        $courses = \App\Models\Course::whereIn(
            'course_id',
            \App\Models\CourseRegistration::where('student_id', $studentId)->pluck('course_id')
        )->get(['course_id', 'course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function getSemesters($studentId, $courseId)
    {
        $semesters = \App\Models\ExamResult::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->pluck('semester')
            ->unique()
            ->sort()
            ->values();
        return response()->json(['success' => true, 'semesters' => $semesters]);
    }

    public function getModuleResults($studentId, $courseId, $semester)
    {
        $results = \App\Models\ExamResult::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('semester', $semester)
            ->with('module')
            ->get()
            ->map(function ($r) {
                return [
                    'module_name' => $r->module->module_name ?? 'N/A',
                    'marks' => $r->marks,
                    'grade' => $r->grade,
                ];
            });
        return response()->json(['success' => true, 'results' => $results]);
    }

    public function getPaymentSummary($studentId, $courseId)
    {
        try {
            // Find student by ID
            $student = Student::find($studentId);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
            }

            // Get course registration for this student and course
            $registration = CourseRegistration::where('student_id', $student->student_id)
                ->where('course_id', $courseId)
                ->with(['course', 'intake'])
                ->first();
            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], 404);
            }

            // Get all payment records for this student and course registration
            $payments = \App\Models\PaymentDetail::where('student_id', $student->student_id)
                ->where('course_registration_id', $registration->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get student-specific payment plan (with discounts and loans applied)
            $studentPaymentPlan = \App\Models\StudentPaymentPlan::where('student_id', $student->student_id)
                ->where('course_id', $registration->course_id)
                ->with(['installments', 'discounts'])
                ->first();

            // Calculate course fees from student payment plan
            $courseFee = 0;
            $franchiseFee = 0;
            $registrationFee = $registration->intake->registration_fee ?? 0;

            if ($studentPaymentPlan) {
                $totalCourseAmount = $studentPaymentPlan->final_amount;
                foreach ($studentPaymentPlan->installments as $installment) {
                    $courseFee += $installment->final_amount ?? $installment->amount ?? 0;
                }
            } else {
                $paymentPlan = \App\Models\PaymentPlan::where('course_id', $registration->course_id)
                    ->where('intake_id', $registration->intake_id)
                    ->first();
                if ($paymentPlan && $paymentPlan->installments) {
                    $installmentsData = $paymentPlan->installments;
                    if (is_string($installmentsData)) {
                        $installmentsData = json_decode($installmentsData, true);
                    }
                    if (is_array($installmentsData)) {
                        foreach ($installmentsData as $installment) {
                            $courseFee += $installment['local_amount'] ?? 0;
                            $franchiseFee += $installment['international_amount'] ?? 0;
                        }
                    }
                }
                $totalCourseAmount = $courseFee + $franchiseFee + $registrationFee;
            }

            // Group payments by type
            $paymentTypes = [
                'course_fee' => ['name' => 'Course Fee', 'total' => $courseFee, 'paid' => 0, 'payments' => []],
                'franchise_fee' => ['name' => 'Franchise Fee', 'total' => $franchiseFee, 'paid' => 0, 'payments' => []],
                'registration_fee' => ['name' => 'Registration Fee', 'total' => $registrationFee, 'paid' => 0, 'payments' => []],
                'library_fee' => ['name' => 'Library Fee', 'total' => 0, 'paid' => 0, 'payments' => []],
                'hostel_fee' => ['name' => 'Hostel Fee', 'total' => 0, 'paid' => 0, 'payments' => []],
                'other' => ['name' => 'Other', 'total' => 0, 'paid' => 0, 'payments' => []],
            ];

            // Process payments
            $totalPaid = 0;
            $paymentHistory = [];
            foreach ($payments as $payment) {
                $paymentType = $payment->payment_type ?? 'course_fee';
                $amount = $payment->amount;
                $categorizedType = $this->categorizePaymentType($paymentType);
                if (isset($paymentTypes[$categorizedType])) {
                    $paymentTypes[$categorizedType]['paid'] += $amount;
                    $paymentTypes[$categorizedType]['payments'][] = $payment;
                } else {
                    $paymentTypes['other']['paid'] += $amount;
                    $paymentTypes['other']['payments'][] = $payment;
                }
                $totalPaid += $amount;
                $paymentHistory[] = [
                    'payment_date' => $payment->created_at->format('Y-m-d'),
                    'payment_type' => $this->getPaymentTypeDisplay($paymentType),
                    'amount' => $amount,
                    'payment_method' => $payment->payment_method,
                    'receipt_no' => $payment->transaction_id,
                    'status' => $payment->status === 'paid' ? 'Paid' : 'Pending'
                ];
            }

            // Calculate summary for each payment type
            $paymentDetails = [];
            foreach ($paymentTypes as $type => $data) {
                if ($data['total'] > 0 || $data['paid'] > 0) {
                    $outstanding = $data['total'] - $data['paid'];
                    $paymentRate = $data['total'] > 0 ? round(($data['paid'] / $data['total']) * 100, 2) : 0;
                    $installmentCount = count(array_filter($data['payments'], function($p) {
                        return !empty($p->installment_number);
                    }));
                    $lastPayment = collect($data['payments'])->sortByDesc('created_at')->first();
                    $lastPaymentDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : null;

                    $detailedPayments = [];
                    foreach ($data['payments'] as $payment) {
                        $detailedPayments[] = [
                            'total_amount' => $data['total'],
                            'paid_amount' => $payment->amount,
                            'outstanding' => $data['total'] - $data['paid'],
                            'payment_date' => $payment->created_at->format('Y-m-d'),
                            'due_date' => $payment->due_date ? $payment->due_date->format('Y-m-d') : null,
                            'receipt_no' => $payment->transaction_id,
                            'uploaded_receipt' => $payment->paid_slip_path ? asset('storage/' . $payment->paid_slip_path) : null,
                            'installment_number' => $payment->installment_number,
                            'payment_method' => $payment->payment_method,
                            'status' => $payment->status === 'paid' ? 'Paid' : 'Pending'
                        ];
                    }

                    $paymentDetails[] = [
                        'payment_type' => strtolower($type),
                        'total_amount' => $data['total'],
                        'paid_amount' => $data['paid'],
                        'outstanding' => $outstanding,
                        'payment_rate' => $paymentRate,
                        'installment_count' => $installmentCount,
                        'last_payment_date' => $lastPaymentDate,
                        'payments' => $detailedPayments
                    ];
                }
            }

            $totalOutstanding = $totalCourseAmount - $totalPaid;
            $overallPaymentRate = $totalCourseAmount > 0 ? round(($totalPaid / $totalCourseAmount) * 100, 2) : 0;

            $summary = [
                'student' => [
                    'student_id' => $student->student_id,
                    'student_name' => $student->full_name,
                    'course_name' => $registration->course->course_name,
                    'registration_date' => $registration->registration_date ? $registration->registration_date->format('Y-m-d') : '',
                    'total_amount' => $totalCourseAmount
                ],
                'total_amount' => $totalCourseAmount,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
                'payment_rate' => $overallPaymentRate,
                'payment_details' => $paymentDetails,
                'payment_history' => $paymentHistory
            ];

            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // Helper: categorize payment type
    private function categorizePaymentType($paymentType)
    {
        $types = [
            'course_fee' => 'course_fee',
            'franchise_fee' => 'franchise_fee',
            'registration_fee' => 'registration_fee',
            'library_fee' => 'library_fee',
            'hostel_fee' => 'hostel_fee',
            'other' => 'other',
        ];
        return $types[$paymentType] ?? 'other';
    }

    // Helper: display name for payment type
    private function getPaymentTypeDisplay($paymentType)
    {
        $types = [
            'course_fee' => 'Course Fee',
            'franchise_fee' => 'Franchise Fee',
            'registration_fee' => 'Registration Fee',
            'library_fee' => 'Library Fee',
            'hostel_fee' => 'Hostel Fee',
            'other' => 'Other',
        ];
        return $types[$paymentType] ?? ucfirst(str_replace('_', ' ', $paymentType));
    }


    // API: Get attendance records for a specific student, course, and semester
    public function getAttendance($studentId, $courseId, $semester)
    {
        $attendance = \App\Models\Attendance::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('semester', $semester)
            ->with('module')
            ->get()
            ->groupBy('module_id')
            ->map(function ($records, $moduleId) {
                $moduleName = $records->first()->module->module_name ?? 'N/A';
                $totalDays = $records->count();
                $presentDays = $records->where('status', true)->count();
                $absentDays = $records->where('status', false)->count();
                $attendancePercent = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
                return [
                    'module_name' => $moduleName,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'attendance_percent' => $attendancePercent
                ];
            })->values();

        return response()->json(['success' => true, 'attendance' => $attendance]);
    }

    public function getStudentClearances($studentId)
    {
        $clearances = \App\Models\ClearanceRequest::where('student_id', $studentId)
            ->get()
            ->map(function ($c) {
                return [
                    'label' => $c->getClearanceTypeTextAttribute(),
                    'status' => $c->status === \App\Models\ClearanceRequest::STATUS_APPROVED,
                    'approved_date' => $c->approved_at ? $c->approved_at->format('d/m/Y') : null,
                    'remarks' => $c->remarks,
                    'clearance_slip' => $c->clearance_slip,
                ];
            });

        return response()->json([
            'success' => true,
            'clearances' => $clearances
        ]);
    }


    public function getStudentCertificates($studentId)
    {
        $student = \App\Models\Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // Get latest OL and AL exam records
        $ol_exam = \App\Models\StudentExam::where('student_id', $studentId)
            ->whereNotNull('ol_certificate')
            ->orderByDesc('created_at')
            ->first();

        $al_exam = \App\Models\StudentExam::where('student_id', $studentId)
            ->whereNotNull('al_certificate')
            ->orderByDesc('created_at')
            ->first();

        $otherInfo = \App\Models\StudentOtherInformation::where('student_id', $studentId)->first();

        $ol_cert = $ol_exam && !empty($ol_exam->ol_certificate) ? $ol_exam->ol_certificate : null;
        $al_cert = $al_exam && !empty($al_exam->al_certificate) ? $al_exam->al_certificate : null;
        $disciplinary_doc = $otherInfo && !empty($otherInfo->disciplinary_issue_document) ? $otherInfo->disciplinary_issue_document : null;

        return response()->json([
            'success' => true,
            'ol_certificate' => $ol_cert,
            'al_certificate' => $al_cert,
            'disciplinary_issue_document' => $disciplinary_doc,
        ]);
    }

    // API: Get student status history (terminate / reinstate logs)
    public function getStudentStatusHistory($studentId)
    {
        $history = \App\Models\StudentStatusHistory::where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'from_status' => $h->from_status,
                    'to_status' => $h->to_status,
                    'reason' => $h->reason,
                    'document' => $h->document,
                    'changed_by' => $h->changed_by,
                    'changed_by_name' => $h->user ? ($h->user->name ?? null) : null,
                    'created_at' => $h->created_at ? $h->created_at->format('d/m/Y H:i') : null,
                ];
            });

        return response()->json(['success' => true, 'history' => $history]);
    }

    public function terminate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'reason'     => 'required|string|max:2000',
            'document'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $student = \App\Models\Student::where('student_id', $request->student_id)->firstOrFail();

        // optional: store doc
        $path = $request->file('document')?->store('termination_docs', 'public');

        // save history (create a model/table if you want an audit trail)
        \App\Models\StudentStatusHistory::create([
            'student_id' => $student->student_id,
            'from_status' => $student->academic_status ?? 'active',
            'to_status'  => 'terminated',
            'reason'     => $request->reason,
            'document'   => $path,
            'changed_by' => auth()->id(),
        ]);

        $student->academic_status = 'terminated';
        $student->save();

        return response()->json(['success' => true, 'message' => 'Student terminated successfully.']);
    }

    public function reinstate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'reason'     => 'required|string|max:2000',
            'document'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $student = \App\Models\Student::where('student_id', $request->student_id)->firstOrFail();

        $path = $request->file('document')?->store('reinstate_docs', 'public');

        \App\Models\StudentStatusHistory::create([
            'student_id' => $student->student_id,
            'from_status' => $student->academic_status ?? 'terminated',
            'to_status'  => 'active',
            'reason'     => $request->reason,
            'document'   => $path,
            'changed_by' => auth()->id(),
        ]);

        $student->academic_status = 'active';
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Student re-registered successfully.',
            'academic_status' => 'active',
        ]);
    }

    /**
     * Update student profile picture
     */
    public function updateStudentProfilePicture(Request $request, $studentId)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        try {
            // Store file in storage/app/public/student_profile_pictures
            $path = $request->file('profile_picture')->store('student_profile_pictures', 'public');

            // Delete previous file if it exists
            if (!empty($student->user_photo) && Storage::disk('public')->exists($student->user_photo)) {
                try {
                    Storage::disk('public')->delete($student->user_photo);
                } catch (\Throwable $e) {
                    // Log error but don't fail the update
                    Log::warning('Failed to delete old student profile picture', [
                        'student_id' => $studentId,
                        'old_path' => $student->user_photo,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Save new path to student record
            $student->user_photo = $path;
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully.',
                'url' => asset('storage/' . $path),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update student profile picture', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile picture. Please try again.',
            ], 500);
        }
    }
}
