<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;
use App\Models\Student;
use App\Models\ExamResult;
use App\Models\CourseRegistration;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

class RepeatStudentsController extends Controller
{
    /**
     * Show the repeat students management view.
     */
    public function showRepeatStudentsManagement()
    {
        $courses = Course::orderBy('course_name')->get();
        $modules = Module::orderBy('module_name')->get();
        $intakes = Intake::join('courses', 'intakes.course_name', '=', 'courses.course_name')
            ->select('intakes.*', 'courses.course_name as course_display_name')
            ->get()
            ->map(function ($intake) {
                $intake->intake_display_name = $intake->course_display_name . ' - ' . $intake->intake_no;
                return $intake;
            });

        return view('repeat_students', compact('courses', 'modules', 'intakes'));
    }

    /**
     * Get course data including modules, semesters, and years.
     */
    public function getCourseData($courseID)
    {
        try {
            $course = Course::with(['modules'])->find($courseID);

            if ($course) {
                $years = range(1, (int)$course->duration); 
                // Get actual created semesters for this course
                $semesters = \App\Models\Semester::where('course_id', $courseID)
                    ->whereIn('status', ['active', 'upcoming'])
                    ->select('id', 'name')
                    ->get();

                return response()->json([
                    'modules' => $course->modules,
                    'semesters' => $semesters,
                ]);
            }

            return response()->json(['error' => 'Course not found or invalid data.'], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            \Log::error('Error in getCourseData for course ID ' . $courseID . ': ' . $e->getMessage());
            return response()->json(['error' => 'An internal server error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Get student name by ID.
     */
    public function getStudentName(Request $request)
    {
        try {
            $student = Student::where('student_id', $request->input('student_id'))->first();

            if ($student) {
                return response()->json(['success' => true, 'name' => $student->full_name]);
            }
            return response()->json(['success' => false, 'message' => 'Student not found.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get repeat students for exam results.
     */
    public function getRepeatStudentsForExamResults(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'location' => 'required|string',
            'semester' => 'required',
            'module_id' => 'required|integer|exists:modules,module_id',
        ]);

        $students = CourseRegistration::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with(['student', 'examResults' => function($query) use ($request) {
                $query->where('module_id', $request->module_id)
                      ->where('semester', $request->semester);
            }])
            ->get()
            ->filter(function($reg) {
                // Filter students who have failed or need to repeat
                return $reg->examResults->where('grade', 'F')->count() > 0 || 
                       $reg->examResults->where('marks', '<', 40)->count() > 0;
            })
            ->map(function($reg) use ($request) {
                $failedResult = $reg->examResults->where('grade', 'F')->first() ?? 
                               $reg->examResults->where('marks', '<', 40)->first();
                
                return [
                    'registration_id' => $reg->course_registration_id ?? $reg->id,
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->full_name,
                    'previous_marks' => $failedResult ? $failedResult->marks : 'N/A',
                    'previous_grade' => $failedResult ? $failedResult->grade : 'N/A',
                    'repeat_count' => $reg->examResults->where('module_id', $request->module_id)->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Get repeat students for payment processes.
     */
    public function getRepeatStudentsForPayments(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'location' => 'required|string',
        ]);

        $students = CourseRegistration::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with(['student', 'payments'])
            ->get()
            ->filter(function($reg) {
                // Filter students with outstanding payments or repeat payment requirements
                $totalPayments = $reg->payments->sum('payment_amount');
                $courseFee = $reg->course->course_fee ?? 0;
                return $totalPayments < $courseFee || $reg->payments->where('payment_status', false)->count() > 0;
            })
            ->map(function($reg) {
                $totalPayments = $reg->payments->sum('payment_amount');
                $courseFee = $reg->course->course_fee ?? 0;
                $outstanding = $courseFee - $totalPayments;
                
                return [
                    'registration_id' => $reg->course_registration_id ?? $reg->id,
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->full_name,
                    'course_fee' => $courseFee,
                    'paid_amount' => $totalPayments,
                    'outstanding_amount' => $outstanding,
                    'payment_status' => $outstanding <= 0 ? 'Paid' : 'Outstanding',
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Update exam results for repeat students.
     */
    public function updateExamResults(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'semester' => 'required',
                'module_id' => 'required|exists:modules,module_id',
                'results' => 'required|array|min:1',
                'results.*.student_id' => 'required|exists:students,student_id',
                'results.*.marks' => 'required|integer|min:0|max:100',
                'results.*.grade' => 'required|string|max:5',
                'results.*.remarks' => 'nullable|string|max:255',
            ]);

            foreach ($validatedData['results'] as $result) {
                // Update existing result or create new one
                ExamResult::updateOrCreate(
                    [
                        'student_id' => $result['student_id'],
                        'course_id' => $validatedData['course_id'],
                        'module_id' => $validatedData['module_id'],
                        'intake_id' => $validatedData['intake_id'],
                        'location' => $validatedData['location'],
                        'semester' => $validatedData['semester'],
                    ],
                    [
                        'marks' => $result['marks'],
                        'grade' => $result['grade'],
                        'remarks' => $result['remarks'] ?? null,
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Exam results updated successfully.'], Response::HTTP_OK);

        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update payment details for repeat students.
     */
    public function updatePaymentDetails(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'payments' => 'required|array|min:1',
                'payments.*.student_id' => 'required|exists:students,student_id',
                'payments.*.payment_amount' => 'required|numeric|min:0',
                'payments.*.payment_method' => 'required|string',
                'payments.*.payment_date' => 'required|date',
                'payments.*.payment_reference' => 'nullable|string',
                'payments.*.remarks' => 'nullable|string',
            ]);

            foreach ($validatedData['payments'] as $payment) {
                PaymentDetail::create([
                    'student_id' => $payment['student_id'],
                    'course_id' => $request->course_id,
                    'registration_id' => $request->registration_id,
                    'payment_method' => $payment['payment_method'],
                    'payment_amount' => $payment['payment_amount'],
                    'payment_date' => $payment['payment_date'],
                    'payment_reference' => $payment['payment_reference'] ?? null,
                    'payment_status' => true, // Assuming successful payment
                    'payment_type' => 'Repeat Student Payment',
                    'remarks' => $payment['remarks'] ?? null,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Payment details updated successfully.'], Response::HTTP_OK);

        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get intakes for course and location.
     */
    public function getIntakesForCourseAndLocation($courseID, $location)
    {
        try {
            $intakes = Intake::where('course_id', $courseID)
                ->where('location', $location)
                ->get();

            return response()->json(['success' => true, 'intakes' => $intakes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get filtered modules.
     */
    public function getFilteredModules(Request $request)
    {
        try {
            $modules = Module::where('course_id', $request->course_id)->get();
            return response()->json(['success' => true, 'modules' => $modules]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get semesters.
     */
    public function getSemesters(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|integer|exists:courses,course_id',
                'intake_id' => 'required|integer|exists:intakes,intake_id',
            ]);

            $course = \App\Models\Course::find($request->course_id);
            $intake = \App\Models\Intake::find($request->intake_id);

            if (!$course || !$intake) {
                return response()->json(['error' => 'Invalid course or intake.'], 404);
            }

            // Get only semesters that have been created for this course and intake
            $semesters = \App\Models\Semester::where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->whereIn('status', ['active', 'upcoming'])
                ->select('id', 'name')
                ->get();

            return response()->json(['success' => true, 'semesters' => $semesters]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 