<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseRegistration;
use App\Models\Intake;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentPaymentPlan;
use App\Models\PaymentDetail;
use App\Models\PaymentInstallment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CourseChangeController extends Controller
{
    /**
     * Display the course change interface
     */
    public function index()
    {
        return view('registration.course_change');
    }

    /**
     * Find student by NIC
     */
    public function findStudent(Request $request)
    {
        try {
            $request->validate([
                'nic' => 'required|string|max:20'
            ]);

            Log::info('Searching student by NIC: ' . $request->nic);

            $student = Student::where('id_value', $request->nic)
                ->orWhere('student_id', $request->nic)
                ->first();

            if (!$student) {
                Log::warning('Student not found: ' . $request->nic);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found. Please check the NIC and try again.'
                ], 404);
            }

            $today = now()->toDateString();

            // Get registrations with proper relationships
            $registrations = CourseRegistration::where('student_id', $student->student_id)
                ->where('status', 'Registered')
                ->with(['course' => function($query) {
                    $query->select('course_id', 'course_name', 'location', 'course_type');
                }, 'intake' => function($query) {
                    $query->select('intake_id', 'batch', 'start_date', 'course_name');
                }])
                ->orderBy('course_start_date', 'desc')
                ->get()
                ->map(function($reg) use ($today) {
                    $reg->is_future = $reg->course_start_date >= $today;
                    return $reg;
                });

            Log::info('Found ' . $registrations->count() . ' registrations for student: ' . $student->student_id);

            return response()->json([
                'status' => 'success',
                'student' => [
                    'student_id' => $student->student_id,
                    'full_name' => $student->full_name,
                    'id_value' => $student->id_value,
                    'email' => $student->email,
                    'phone' => $student->phone_number
                ],
                'registrations' => $registrations,
                'count' => $registrations->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error finding student: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active courses for dropdown
     */
    public function getCourses()
    {
        try {
            $coursesQuery = Course::select('course_id', 'course_name', 'location', 'course_type');
            if (Schema::hasColumn('courses', 'status')) {
                $coursesQuery->where(function($query) {
                    $query->where('status', 'active')
                          ->orWhereNull('status');
                });
            }

            $courses = $coursesQuery
                ->orderBy('location')
                ->orderBy('course_name')
                ->get();

            Log::info('Retrieved ' . $courses->count() . ' courses');

            return response()->json([
                'status' => 'success',
                'courses' => $courses,
                'count' => $courses->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting courses: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load courses',
                'courses' => []
            ], 500);
        }
    }

    /**
     * Get intakes for selected course
     */
    public function getNewIntakes(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|integer|exists:courses,course_id'
            ]);

            Log::info('Getting intakes for course: ' . $request->course_id);

            $intakesQuery = Intake::where('course_id', $request->course_id);
            if (Schema::hasColumn('intakes', 'status')) {
                $intakesQuery->where(function($query) {
                    $query->where('status', 'active')
                          ->orWhereNull('status');
                });
            }

            $intakes = $intakesQuery
                ->orderBy('start_date', 'desc')
                ->get();

            Log::info('Found ' . $intakes->count() . ' intakes for course: ' . $request->course_id);

            return response()->json([
                'status' => 'success',
                'intakes' => $intakes,
                'count' => $intakes->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting intakes: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load intakes',
                'intakes' => []
            ], 500);
        }
    }

    /**
     * Generate new course registration ID
     */
    public function generateNewCourseRegId(Request $request)
    {
        try {
            $request->validate([
                'intake_id' => 'required|integer|exists:intakes,intake_id'
            ]);

            Log::info('Generating ID for intake: ' . $request->intake_id);

            $intake = Intake::find($request->intake_id);

            if (!$intake) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Intake not found'
                ], 404);
            }

            // Check if pattern exists
            if (!$intake->course_registration_id_pattern) {
                // Generate default pattern if not exists
                $course = Course::find($intake->course_id);
                $pattern = strtoupper(substr($course->location, 0, 3)) . '/' . 
                          date('Y') . '/' . 
                          strtoupper(substr($course->course_type, 0, 3)) . '/' .
                          '001';
                
                // Update intake with generated pattern
                $intake->update(['course_registration_id_pattern' => $pattern]);
                Log::info('Generated default pattern: ' . $pattern);
            }

            $pattern = $intake->course_registration_id_pattern;

            // Extract prefix and number
            if (!preg_match('/^(.*?)(\d+)$/', $pattern, $matches)) {
                // If no number at end, add 001
                $prefix = $pattern;
                $digits = 3;
                $next = '001';
                $newId = $prefix . $next;
                
                Log::info('Generated ID (no pattern): ' . $newId);
                
                return response()->json([
                    'status' => 'success',
                    'new_id' => $newId
                ]);
            }

            $prefix = $matches[1];
            $baseNum = $matches[2];
            $digits = strlen($baseNum);

            // Get existing IDs with same prefix
            $existing = CourseRegistration::where('course_registration_id', 'LIKE', $prefix . '%')
                ->pluck('course_registration_id')
                ->toArray();

            if (count($existing) === 0) {
                Log::info('No existing IDs found, using base pattern: ' . $pattern);
                return response()->json([
                    'status' => 'success',
                    'new_id' => $pattern
                ]);
            }

            // Find maximum number
            $max = 0;
            foreach ($existing as $id) {
                if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $id, $m)) {
                    $num = intval($m[1]);
                    if ($num > $max) {
                        $max = $num;
                    }
                }
            }

            $next = str_pad($max + 1, $digits, '0', STR_PAD_LEFT);
            $newId = $prefix . $next;

            Log::info('Generated new ID: ' . $newId . ' (prefix: ' . $prefix . ', next: ' . $next . ')');

            return response()->json([
                'status' => 'success',
                'new_id' => $newId
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating ID: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate registration ID: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status for a registration
     */
    /**
 * Check payment status for a registration
 */
public function checkPaymentStatus(Request $request)
{
    try {
        Log::info('=== START PAYMENT CHECK ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'registration_id' => 'required|integer'
        ]);

        // First check if registration exists
        $registration = CourseRegistration::find($request->registration_id);
        
        if (!$registration) {
            Log::warning('Registration not found: ' . $request->registration_id);
            return response()->json([
                'status' => 'error',
                'message' => 'Registration not found with ID: ' . $request->registration_id
            ], 404);
        }

        Log::info('Found registration:', [
            'id' => $registration->id,
            'student_id' => $registration->student_id,
            'course_id' => $registration->course_id,
            'status' => $registration->status
        ]);

        // Check payment plan
        $paymentPlan = StudentPaymentPlan::where('student_id', $registration->student_id)
            ->where('course_id', $registration->course_id)
            ->where('status', 'active')
            ->first();

        Log::info('Payment plan search:', [
            'student_id' => $registration->student_id,
            'course_id' => $registration->course_id,
            'found' => $paymentPlan ? 'Yes' : 'No'
        ]);

        if (!$paymentPlan) {
            Log::info('No active payment plan found');
            return response()->json([
                'status' => 'success',
                'has_payment_plan' => false,
                'has_payments' => false,
                'total_paid_amount' => 0,
                'message' => 'No active payment plan found for this course.'
            ]);
        }

        Log::info('Payment plan found:', [
            'id' => $paymentPlan->id,
            'total_amount' => $paymentPlan->total_amount,
            'final_amount' => $paymentPlan->final_amount
        ]);

        // Check payment details
        $paymentDetails = PaymentDetail::where('student_id', $registration->student_id)
            ->where('course_registration_id', $registration->id)
            ->get();

        Log::info('Found ' . $paymentDetails->count() . ' payment details');

        $totalPaidAmount = 0;
        $hasPayments = false;

        foreach ($paymentDetails as $paymentDetail) {
            $partialPreview = is_string($paymentDetail->partial_payments)
                ? substr($paymentDetail->partial_payments, 0, 100)
                : json_encode($paymentDetail->partial_payments);

            Log::info('Processing payment detail:', [
                'id' => $paymentDetail->id,
                'amount' => $paymentDetail->amount,
                'status' => $paymentDetail->status,
                'partial_payments' => $partialPreview
            ]);

            $paidAmount = $this->getPaidAmountFromDetail($paymentDetail);

            if ($paidAmount > 0) {
                $totalPaidAmount += $paidAmount;
                $hasPayments = true;
                Log::info('Accumulated payment amount:', [
                    'amount' => $paidAmount,
                    'total_so_far' => $totalPaidAmount
                ]);
            }
        }

        Log::info('Payment check complete:', [
            'total_paid_amount' => $totalPaidAmount,
            'has_payments' => $hasPayments
        ]);

        $response = [
            'status' => 'success',
            'has_payment_plan' => true,
            'has_payments' => $hasPayments,
            'total_paid_amount' => $totalPaidAmount,
            'payment_plan_id' => $paymentPlan->id,
            'student_id' => $registration->student_id,
            'debug_info' => [
                'registration_id' => $registration->id,
                'payment_details_count' => $paymentDetails->count()
            ]
        ];

        if ($hasPayments) {
            $response['message'] = "Found payment records with LKR " . number_format($totalPaidAmount, 2) . " paid.";
        } else {
            $response['message'] = "Payment plan exists but no payments have been made yet.";
        }

        Log::info('=== END PAYMENT CHECK ===');
        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('Payment check error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Error checking payment status: ' . $e->getMessage(),
            'error_details' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ], 500);
    }
}

    /**
     * Submit course change request
     */
    public function submitChange(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'registration_id' => 'required|integer|exists:course_registration,id',
                'new_intake_id' => 'required|integer|exists:intakes,intake_id',
                'new_course_registration_id' => 'required|string|max:100'
            ]);

            Log::info('Starting course change process', [
                'registration_id' => $request->registration_id,
                'new_intake_id' => $request->new_intake_id,
                'new_course_registration_id' => $request->new_course_registration_id,
                'user_id' => auth()->id()
            ]);

            $registration = CourseRegistration::with(['course', 'intake'])->find($request->registration_id);
            $newIntake = Intake::with('course')->find($request->new_intake_id);

            if (!$registration || !$newIntake) {
                throw new \Exception('Invalid registration or intake data');
            }

            $oldIntakeId = $registration->intake_id;
            $oldCourseId = $registration->course_id;
            $studentId = $registration->student_id;

            Log::info('Course change details', [
                'student_id' => $studentId,
                'old_course_id' => $oldCourseId,
                'old_intake_id' => $oldIntakeId,
                'new_course_id' => $newIntake->course_id,
                'new_intake_id' => $newIntake->intake_id
            ]);

            // Check if course change is within 1 year
            $courseStartDate = Carbon::parse($registration->course_start_date);
            $currentDate = Carbon::now();
            
            if ($courseStartDate->diffInYears($currentDate) >= 1) {
                throw new \Exception('Course change is only allowed within 1 year from course start date. Course started on ' . $courseStartDate->format('Y-m-d'));
            }

            // Process payment records
            $paymentProcessResult = $this->processPaymentRecords($studentId, $oldCourseId, $registration->id, $oldIntakeId);

            // Update course registration
            $registration->course_id = $newIntake->course_id;
            $registration->intake_id = $newIntake->intake_id;
            $registration->course_start_date = $newIntake->start_date;
            $registration->course_registration_id = $request->new_course_registration_id;
            $registration->updated_at = now();
            $registration->save();

            Log::info('Course registration updated successfully');

            // Log the course change
            $logId = $this->logCourseChange(
                $studentId,
                $oldIntakeId,
                $oldCourseId,
                $newIntake->intake_id,
                $newIntake->course_id,
                $paymentProcessResult['old_payment_plan_id'] ?? null,
                $paymentProcessResult['total_paid_amount'] ?? 0
            );

            Log::info('Course change logged with ID: ' . $logId);

            // Create new payment plan for new course if needed
            $this->createNewPaymentPlan($studentId, $newIntake->course_id);

            DB::commit();

            Log::info('Course change completed successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Course changed successfully!',
                'payment_summary' => [
                    'total_paid_amount' => $paymentProcessResult['total_paid_amount'] ?? 0,
                    'payment_records_updated' => $paymentProcessResult['records_updated'] ?? 0,
                    'has_payments' => $paymentProcessResult['has_payments'] ?? false
                ],
                'new_course_info' => [
                    'course_name' => $newIntake->course->course_name ?? 'N/A',
                    'intake_batch' => $newIntake->batch,
                    'start_date' => $newIntake->start_date,
                    'registration_id' => $request->new_course_registration_id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Course change error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error changing course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment records for old course
     */
    private function processPaymentRecords($studentId, $oldCourseId, $registrationId, $oldIntakeId = null)
    {
        $result = [
            'total_paid_amount' => 0,
            'records_updated' => 0,
            'has_payments' => false,
            'old_payment_plan_id' => null
        ];

        Log::info('Processing payment records', [
            'student_id' => $studentId,
            'old_course_id' => $oldCourseId,
            'registration_id' => $registrationId
        ]);

        // Find existing payment plan
        $oldPaymentPlan = StudentPaymentPlan::where('student_id', $studentId)
            ->where('course_id', $oldCourseId)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if (!$oldPaymentPlan) {
            Log::info('No active payment plan found for old course');
            return $result;
        }

        $result['old_payment_plan_id'] = $oldPaymentPlan->id;
        Log::info('Found old payment plan: ' . $oldPaymentPlan->id);

        // Find related payment details
        $paymentDetails = PaymentDetail::where('student_id', $studentId)
            ->where('course_registration_id', $registrationId)
            ->get();

        Log::info('Found ' . $paymentDetails->count() . ' payment details');

        // Calculate total partial payments
        foreach ($paymentDetails as $paymentDetail) {
            $paidAmount = $this->getPaidAmountFromDetail($paymentDetail);

            if ($paidAmount > 0) {
                $result['total_paid_amount'] += $paidAmount;
                $result['has_payments'] = true;
            }

            $remarksPrefix = $paymentDetail->remarks ? rtrim($paymentDetail->remarks) . ' | ' : '';
            $paidNote = 'Cancelled due to course change on ' . now()->format('Y-m-d H:i:s');
            if ($paidAmount > 0) {
                $paidNote .= ' | Paid amount: LKR ' . number_format($paidAmount, 2);
            }

            // Update payment detail to cancelled
            $paymentDetail->update([
                'amount' => 0,
                'remaining_amount' => 0,
                'total_fee' => 0,
                'late_fee' => 0,
                'approved_late_fee' => 0,
                'partial_payments' => [],
                'status' => 'cancelled',
                'remarks' => $remarksPrefix . $paidNote,
                'updated_at' => now()
            ]);

            $result['records_updated']++;
            Log::debug('Updated payment detail: ' . $paymentDetail->id);
        }

        // Update payment installments
        $installmentsUpdated = PaymentInstallment::where('payment_plan_id', $oldPaymentPlan->id)
            ->update([
                'status' => 'archived',
                'updated_at' => now()
            ]);

        Log::info('Updated ' . $installmentsUpdated . ' payment installments');

        // Update old payment plan
        $oldPaymentPlan->update([
            'status' => 'archived',
            'updated_at' => now()
        ]);

        Log::info('Cancelled payment plan: ' . $oldPaymentPlan->id);

        // Store payment summary
        DB::table('course_change_payments')->insert([
            'student_id' => $studentId,
            'old_course_id' => $oldCourseId,
            'old_intake_id' => $oldIntakeId ?? null,
            'old_payment_plan_id' => $oldPaymentPlan->id,
            'total_paid_amount' => $result['total_paid_amount'],
            'remarks' => 'Payment records cancelled due to course change',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info('Payment processing complete', $result);

        return $result;
    }

    private function getPaidAmountFromDetail(PaymentDetail $paymentDetail)
    {
        $partialTotal = $this->sumPartialPayments($paymentDetail->partial_payments);
        if ($partialTotal > 0) {
            return $partialTotal;
        }

        if ($paymentDetail->status === 'paid') {
            $totalFee = $paymentDetail->total_fee ?? $paymentDetail->amount ?? 0;
            $remaining = $paymentDetail->remaining_amount ?? null;
            if (is_numeric($remaining)) {
                $totalFee = max(0, (float)$totalFee - (float)$remaining);
            }
            return max(0, (float)$totalFee);
        }

        return 0;
    }

    private function sumPartialPayments($partialPayments)
    {
        $partials = $this->normalizePartialPayments($partialPayments);
        $total = 0;

        foreach ($partials as $partial) {
            if (is_array($partial) && isset($partial['amount']) && is_numeric($partial['amount'])) {
                $total += (float)$partial['amount'];
            }
        }

        return $total;
    }

    private function normalizePartialPayments($partialPayments)
    {
        if (is_array($partialPayments)) {
            return $partialPayments;
        }

        if (!is_string($partialPayments)) {
            return [];
        }

        $trimmed = trim($partialPayments);
        if ($trimmed === '' || $trimmed === '[]' || $trimmed === '""' || strtolower($trimmed) === 'null') {
            return [];
        }

        $decoded = json_decode($trimmed, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Create new payment plan for new course
     */
    private function createNewPaymentPlan($studentId, $newCourseId)
    {
        try {
            // Check if payment plan already exists for new course
            $existingPlan = StudentPaymentPlan::where('student_id', $studentId)
                ->where('course_id', $newCourseId)
                ->where('status', 'active')
                ->first();

            if ($existingPlan) {
                Log::info('Payment plan already exists for new course: ' . $existingPlan->id);
                return $existingPlan;
            }

            // Get course details to set default amounts
            $course = Course::find($newCourseId);
            if (!$course) {
                Log::warning('Course not found for new payment plan: ' . $newCourseId);
                return null;
            }

            // Get intake for this course to get fee details
            $intake = Intake::where('course_id', $newCourseId)
                ->orderBy('start_date', 'desc')
                ->first();

            $totalAmount = $intake ? ($intake->course_fee ?? 0) : 0;

            // Create new payment plan
            $newPaymentPlan = StudentPaymentPlan::create([
                'student_id' => $studentId,
                'course_id' => $newCourseId,
                'intake_id' => $intake->intake_id ?? null,
                'payment_plan_type' => 'installments',
                'slt_loan_applied' => 'no',
                'slt_loan_amount' => 0,
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Created new payment plan: ' . $newPaymentPlan->id . ' for course: ' . $newCourseId);

            return $newPaymentPlan;

        } catch (\Exception $e) {
            Log::error('Error creating new payment plan: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log course change with payment info
     */
    private function logCourseChange($studentId, $oldIntakeId, $oldCourseId, $newIntakeId, $newCourseId, $oldPaymentPlanId, $totalPaidAmount)
    {
        return DB::table('course_change_logs')->insertGetId([
            'student_id' => $studentId,
            'old_intake_id' => $oldIntakeId,
            'old_course_id' => $oldCourseId,
            'new_intake_id' => $newIntakeId,
            'new_course_id' => $newCourseId,
            'old_payment_plan_id' => $oldPaymentPlanId,
            'total_paid_amount' => $totalPaidAmount,
            'changed_by' => auth()->id(),
            'changed_by_name' => auth()->user()->name ?? 'System',
            'changed_at' => now(),
            'remarks' => 'Course change processed',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Get payment summary for a course change
     */
    public function getPaymentSummary($studentId, $courseId)
    {
        try {
            Log::info('Getting payment summary for student: ' . $studentId . ', course: ' . $courseId);

            $summary = DB::table('course_change_payments')
                ->where('student_id', $studentId)
                ->where('old_course_id', $courseId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$summary) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No payment summary found'
                ], 404);
            }

            // Get student info
            $student = Student::find($studentId);
            $course = Course::find($courseId);

            return response()->json([
                'status' => 'success',
                'summary' => $summary,
                'student' => $student ? [
                    'name' => $student->full_name,
                    'id' => $student->student_id
                ] : null,
                'course' => $course ? [
                    'name' => $course->course_name
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment summary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get payment summary'
            ], 500);
        }
    }

    /**
     * Get course change logs for student
     */
    public function getChangeLogs($studentId)
    {
        try {
            $logs = DB::table('course_change_logs')
                ->where('student_id', $studentId)
                ->orderBy('changed_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'logs' => $logs,
                'count' => $logs->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting change logs: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get change logs'
            ], 500);
        }
    }

    public function getCancelledPayments($studentId)
    {
        try {
            $payments = PaymentDetail::where('student_id', $studentId)
                ->where('status', 'cancelled')
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'course_registration_id', 'remarks', 'status', 'updated_at']);

            return response()->json([
                'status' => 'success',
                'payments' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cancelled payments: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get cancelled payments'
            ], 500);
        }
    }

    public function updateCancelledPaymentStatus(Request $request, $paymentDetailId)
    {
        try {
            $request->validate([
                'status' => 'required|in:cancelled,pending'
            ]);

            $payment = PaymentDetail::find($paymentDetailId);
            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment record not found'
                ], 404);
            }

            $today = now()->toDateString();
            $statusNote = $request->status === 'cancelled'
                ? "Payment Updated {$today}"
                : "Pending the Payment Update";

            $remarksBase = $payment->remarks ?? '';
            $remarksBase = preg_replace(
                '/\s*\|\s*(Payment Updated\s\d{4}-\d{2}-\d{2}|Pending the Payment Update)\s*$/',
                '',
                trim($remarksBase)
            );
            $remarksBase = trim($remarksBase, " \t\n\r\0\x0B|");
            $remarksBase = $remarksBase === '' ? '' : $remarksBase;
            $note = $remarksBase === '' ? $statusNote : $remarksBase . ' | ' . $statusNote;

            $payment->update([
                'remarks' => $note,
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating cancelled payment status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update payment'
            ], 500);
        }
    }

    public function getCourseChangeHistory($studentId)
    {
        try {
            $logs = DB::table('course_change_logs')
                ->where('student_id', $studentId)
                ->orderBy('changed_at', 'desc')
                ->get();

            $payments = DB::table('course_change_payments')
                ->where('student_id', $studentId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'logs' => $logs,
                'payments' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting course change history: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load course change history'
            ], 500);
        }
    }
}
