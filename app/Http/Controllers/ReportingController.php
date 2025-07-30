<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Intake;
use App\Models\Module;
use App\Models\ModuleManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingController extends Controller
{
    /**
     * Show the reporting dashboard
     */
    public function showReportingDashboard()
    {
        if (!Auth::check() || !Auth::user()->status) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return view('reporting.dashboard');
    }

    /**
     * Show the reporting dashboard (alias for showReportingDashboard)
     */
    public function showDashboard()
    {
        return $this->showReportingDashboard();
    }

    /**
     * Generate student enrollment report
     */
    public function generateStudentEnrollmentReport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'location' => 'nullable|string|in:Welisara,Moratuwa,Peradeniya',
                'course_id' => 'nullable|exists:courses,course_id',
                'format' => 'nullable|string|in:json,pdf,excel'
            ]);

            $query = Student::query();

            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('location')) {
                $query->where('institute_location', $request->location);
            }

            $students = $query->with(['courseRegistrations.course', 'courseRegistrations.intake'])
                            ->get();

            // Group by location
            $locationStats = $students->groupBy('institute_location')
                                    ->map(function ($group) {
                                        return [
                                            'count' => $group->count(),
                                            'male' => $group->where('gender', 'Male')->count(),
                                            'female' => $group->where('gender', 'Female')->count(),
                                            'students' => $group->map(function ($student) {
                                                return [
                                                    'student_id' => $student->student_id,
                                                    'name' => $student->full_name,
                                                    'email' => $student->email,
                                                    'location' => $student->institute_location,
                                                    'registration_date' => $student->created_at->format('Y-m-d'),
                                                    'courses' => $student->courseRegistrations->map(function ($reg) {
                                                        return [
                                                            'course_name' => $reg->course->course_name ?? 'N/A',
                                                            'intake_name' => $reg->intake->intake_name ?? 'N/A',
                                                            'registration_date' => $reg->created_at->format('Y-m-d')
                                                        ];
                                                    })
                                                ];
                                            })
                                        ];
                                    });

            $report = [
                'total_students' => $students->count(),
                'male_students' => $students->where('gender', 'Male')->count(),
                'female_students' => $students->where('gender', 'Female')->count(),
                'location_stats' => $locationStats,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'filters_applied' => $request->only(['start_date', 'end_date', 'location', 'course_id'])
            ];

            if ($request->input('format') === 'json') {
                return response()->json([
                    'success' => true,
                    'data' => $report
                ]);
            }

            // For PDF/Excel, you would implement export logic here
            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully.',
                'data' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Student enrollment report generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.'
            ], 500);
        }
    }

    /**
     * Generate course performance report
     */
    public function generateCoursePerformanceReport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'course_id' => 'nullable|exists:courses,course_id',
                'location' => 'nullable|string|in:Welisara,Moratuwa,Peradeniya',
                'semester' => 'nullable|string|in:1,2,3,4,5,6',
                'format' => 'nullable|string|in:json,pdf,excel'
            ]);

            $query = CourseRegistration::with(['student', 'course', 'intake']);

            // Apply filters
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }

            $registrations = $query->get();

            // Calculate performance metrics
            $courseStats = $registrations->groupBy('course_id')
                                       ->map(function ($group) {
                                           $course = $group->first()->course;
                                           $totalStudents = $group->count();
                                           
                                           // Get attendance data
                                           $attendanceData = Attendance::whereIn('student_id', $group->pluck('student_id'))
                                                                      ->where('course_id', $course->course_id)
                                                                      ->get();
                                           
                                           $avgAttendance = $attendanceData->count() > 0 
                                               ? round(($attendanceData->where('status', 'Present')->count() / $attendanceData->count()) * 100, 2)
                                               : 0;

                                           // Get exam results
                                           $examData = ExamResult::whereIn('student_id', $group->pluck('student_id'))
                                                                ->where('course_id', $course->course_id)
                                                                ->get();

                                           $avgScore = $examData->count() > 0 
                                               ? round($examData->avg('score'), 2)
                                               : 0;

                                           return [
                                               'course_id' => $course->course_id,
                                               'course_name' => $course->course_name,
                                               'total_students' => $totalStudents,
                                               'average_attendance' => $avgAttendance,
                                               'average_exam_score' => $avgScore,
                                               'completion_rate' => $this->calculateCompletionRate($group),
                                               'students' => $group->map(function ($reg) {
                                                   return [
                                                       'student_id' => $reg->student->student_id,
                                                       'student_name' => $reg->student->full_name,
                                                       'registration_date' => $reg->created_at->format('Y-m-d'),
                                                       'status' => $reg->status ?? 'Active'
                                                   ];
                                               })
                                           ];
                                       });

            $report = [
                'total_courses' => $courseStats->count(),
                'total_students' => $registrations->count(),
                'course_performance' => $courseStats,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'filters_applied' => $request->only(['course_id', 'location', 'semester'])
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Course performance report generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.'
            ], 500);
        }
    }

    /**
     * Generate attendance report
     */
    public function generateAttendanceReport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'course_id' => 'nullable|exists:courses,course_id',
                'location' => 'nullable|string|in:Welisara,Moratuwa,Peradeniya',
                'format' => 'nullable|string|in:json,pdf,excel'
            ]);

            $query = Attendance::with(['student', 'course']);

            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }

            $attendance = $query->get();

            // Calculate attendance statistics
            $totalSessions = $attendance->count();
            $presentSessions = $attendance->where('status', 'Present')->count();
            $absentSessions = $attendance->where('status', 'Absent')->count();
            $lateSessions = $attendance->where('status', 'Late')->count();

            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 2) : 0;

            // Group by course
            $courseStats = $attendance->groupBy('course_id')
                                    ->map(function ($group) {
                                        $course = $group->first()->course;
                                        $total = $group->count();
                                        $present = $group->where('status', 'Present')->count();
                                        $absent = $group->where('status', 'Absent')->count();
                                        $late = $group->where('status', 'Late')->count();

                                        return [
                                            'course_id' => $course->course_id,
                                            'course_name' => $course->course_name,
                                            'total_sessions' => $total,
                                            'present' => $present,
                                            'absent' => $absent,
                                            'late' => $late,
                                            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0
                                        ];
                                    });

            // Group by student
            $studentStats = $attendance->groupBy('student_id')
                                     ->map(function ($group) {
                                         $student = $group->first()->student;
                                         $total = $group->count();
                                         $present = $group->where('status', 'Present')->count();
                                         $absent = $group->where('status', 'Absent')->count();
                                         $late = $group->where('status', 'Late')->count();

                                         return [
                                             'student_id' => $student->student_id,
                                             'student_name' => $student->full_name,
                                             'total_sessions' => $total,
                                             'present' => $present,
                                             'absent' => $absent,
                                             'late' => $late,
                                             'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0
                                         ];
                                     });

            $report = [
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'absent_sessions' => $absentSessions,
                'late_sessions' => $lateSessions,
                'overall_attendance_rate' => $attendanceRate,
                'course_statistics' => $courseStats,
                'student_statistics' => $studentStats,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'filters_applied' => $request->only(['start_date', 'end_date', 'course_id', 'location'])
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance report generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.'
            ], 500);
        }
    }

    /**
     * Generate financial report
     */
    public function generateFinancialReport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'location' => 'nullable|string|in:Welisara,Moratuwa,Peradeniya',
                'format' => 'nullable|string|in:json,pdf,excel'
            ]);

            // Get course registrations with payment information
            $query = CourseRegistration::with(['student', 'course', 'intake']);

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }

            $registrations = $query->get();

            // Calculate financial metrics
            $totalRevenue = $registrations->sum('payment_amount');
            $totalStudents = $registrations->count();
            $averagePayment = $totalStudents > 0 ? round($totalRevenue / $totalStudents, 2) : 0;

            // Group by course
            $courseRevenue = $registrations->groupBy('course_id')
                                         ->map(function ($group) {
                                             $course = $group->first()->course;
                                             $revenue = $group->sum('payment_amount');
                                             $students = $group->count();

                                             return [
                                                 'course_id' => $course->course_id,
                                                 'course_name' => $course->course_name,
                                                 'total_revenue' => $revenue,
                                                 'student_count' => $students,
                                                 'average_payment' => $students > 0 ? round($revenue / $students, 2) : 0
                                             ];
                                         });

            // Group by location
            $locationRevenue = $registrations->groupBy('location')
                                           ->map(function ($group) {
                                               $revenue = $group->sum('payment_amount');
                                               $students = $group->count();

                                               return [
                                                   'location' => $group->first()->location,
                                                   'total_revenue' => $revenue,
                                                   'student_count' => $students,
                                                   'average_payment' => $students > 0 ? round($revenue / $students, 2) : 0
                                               ];
                                           });

            // Monthly revenue breakdown
            $monthlyRevenue = $registrations->groupBy(function ($reg) {
                return $reg->created_at->format('Y-m');
            })->map(function ($group) {
                return [
                    'month' => $group->first()->created_at->format('Y-m'),
                    'revenue' => $group->sum('payment_amount'),
                    'students' => $group->count()
                ];
            });

            $report = [
                'total_revenue' => $totalRevenue,
                'total_students' => $totalStudents,
                'average_payment' => $averagePayment,
                'course_revenue' => $courseRevenue,
                'location_revenue' => $locationRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'filters_applied' => $request->only(['start_date', 'end_date', 'location'])
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Financial report generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.'
            ], 500);
        }
    }

    /**
     * Generate module assignment report
     */
    public function generateModuleAssignmentReport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'course_id' => 'nullable|exists:courses,course_id',
                'location' => 'nullable|string|in:Welisara,Moratuwa,Peradeniya',
                'semester' => 'nullable|string|in:1,2,3,4,5,6',
                'format' => 'nullable|string|in:json,pdf,excel'
            ]);

            $query = ModuleManagement::with(['student', 'course', 'module', 'intake']);

            // Apply filters
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }
            if ($request->filled('semester')) {
                $query->where('semester', $request->semester);
            }

            $assignments = $query->get();

            // Group by module
            $moduleStats = $assignments->groupBy('module_id')
                                     ->map(function ($group) {
                                         $module = $group->first()->module;
                                         $course = $group->first()->course;

                                         return [
                                             'module_id' => $module->module_id,
                                             'module_name' => $module->module_name,
                                             'course_name' => $course->course_name,
                                             'student_count' => $group->count(),
                                             'students' => $group->map(function ($assignment) {
                                                 return [
                                                     'student_id' => $assignment->student->student_id,
                                                     'student_name' => $assignment->student->full_name,
                                                     'semester' => $assignment->semester
                                                 ];
                                             })
                                         ];
                                     });

            // Group by semester
            $semesterStats = $assignments->groupBy('semester')
                                       ->map(function ($group) {
                                           return [
                                               'semester' => $group->first()->semester,
                                               'total_assignments' => $group->count(),
                                               'unique_students' => $group->unique('student_id')->count(),
                                               'unique_modules' => $group->unique('module_id')->count()
                                           ];
                                       });

            $report = [
                'total_assignments' => $assignments->count(),
                'unique_students' => $assignments->unique('student_id')->count(),
                'unique_modules' => $assignments->unique('module_id')->count(),
                'module_statistics' => $moduleStats,
                'semester_statistics' => $semesterStats,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'filters_applied' => $request->only(['course_id', 'location', 'semester'])
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Module assignment report generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.'
            ], 500);
        }
    }

    /**
     * Export report to different formats
     */
    public function exportReport(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'report_type' => 'required|string|in:enrollment,performance,attendance,financial,module',
                'format' => 'required|string|in:pdf,excel,csv',
                'filters' => 'nullable|array'
            ]);

            $reportType = $request->input('report_type');
            $format = $request->input('format');
            $filters = $request->input('filters', []);

            // Generate report based on type
            switch ($reportType) {
                case 'enrollment':
                    $data = $this->generateStudentEnrollmentReport(new Request($filters));
                    break;
                case 'performance':
                    $data = $this->generateCoursePerformanceReport(new Request($filters));
                    break;
                case 'attendance':
                    $data = $this->generateAttendanceReport(new Request($filters));
                    break;
                case 'financial':
                    $data = $this->generateFinancialReport(new Request($filters));
                    break;
                case 'module':
                    $data = $this->generateModuleAssignmentReport(new Request($filters));
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid report type.'
                    ], 400);
            }

            if (!$data->getData()->success) {
                return $data;
            }

            $reportData = $data->getData()->data;

            // For now, return JSON. In a real implementation, you would generate PDF/Excel/CSV
            return response()->json([
                'success' => true,
                'message' => 'Report exported successfully.',
                'data' => $reportData,
                'format' => $format,
                'filename' => "{$reportType}_report_" . now()->format('Y-m-d_H-i-s') . ".{$format}"
            ]);

        } catch (\Exception $e) {
            Log::error('Report export failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export report.'
            ], 500);
        }
    }

    /**
     * Calculate completion rate for a group of registrations
     */
    private function calculateCompletionRate($registrations)
    {
        $total = $registrations->count();
        $completed = $registrations->where('status', 'Completed')->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
}
