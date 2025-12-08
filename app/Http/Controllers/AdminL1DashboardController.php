<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminL1DashboardController extends Controller
{
    public function showDashboard()
    {
        return view('adminl1.dashboard');
    }

    public function getOverviewMetrics(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $metrics = [
            'total_students' => DB::table('students')->count(),
            'active_students' => DB::table('students')->where('academic_status', 'active')->count(),
            'new_students_this_period' => DB::table('students')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            
            'total_registrations' => DB::table('course_registration')->count(),
            'pending_registrations' => DB::table('course_registration')->where('status', 'Pending')->count(),
            'completed_registrations' => DB::table('course_registration')->where('status', 'Registered')->count(),
            
            'pending_clearances' => DB::table('clearance_requests')->where('status', 'pending')->count(),
            'total_courses' => DB::table('courses')->count(),
            'active_intakes' => DB::table('intakes')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())->count(),
            
            'pending_payments' => DB::table('payment_details')->where('status', 'pending')->count(),
            'total_revenue_this_period' => DB::table('payment_details')
                ->where('status', 'paid')
                ->whereBetween('payment_date', [$dateRange['start'], $dateRange['end']])
                ->sum('amount'),
            
            'attendance_taken_today' => DB::table('attendance')->whereDate('date', today())->count(),
            'total_users' => DB::table('users')->count(),
        ];

        return response()->json($metrics);
    }

    public function getStudentStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $stats = [
            'by_status' => DB::table('students')
                ->select('academic_status', DB::raw('count(*) as count'))
                ->groupBy('academic_status')->get(),
            
            'by_location' => DB::table('students')
                ->select('institute_location', DB::raw('count(*) as count'))
                ->groupBy('institute_location')->get(),
            
            'registration_trend' => DB::table('students')
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
                ->whereBetween('created_at', [now()->subMonths(12), now()])
                ->groupBy('month')->orderBy('month')->get(),
        ];

        return response()->json($stats);
    }

    public function getCourseRegistrationStats(Request $request)
    {
        $stats = [
            'by_status' => DB::table('course_registration')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')->get(),
            
            'top_courses' => DB::table('course_registration')
                ->join('courses', 'course_registration.course_id', '=', 'courses.course_id')
                ->select('courses.course_name', DB::raw('count(*) as registrations'))
                ->groupBy('courses.course_id', 'courses.course_name')
                ->orderBy('registrations', 'desc')->limit(10)->get(),
        ];

        return response()->json($stats);
    }

    public function getClearanceStats()
    {
        $stats = [
            'by_type' => DB::table('clearance_requests')
                ->select('clearance_type', DB::raw('count(*) as count'))
                ->groupBy('clearance_type')->get(),
            
            'pending_list' => DB::table('clearance_requests')
                ->join('students', 'clearance_requests.student_id', '=', 'students.student_id')
                ->join('courses', 'clearance_requests.course_id', '=', 'courses.course_id')
                ->select('clearance_requests.*', 'students.name_with_initials as student_name', 'courses.course_name')
                ->where('clearance_requests.status', 'pending')
                ->orderBy('clearance_requests.created_at', 'desc')->limit(20)->get(),
        ];

        return response()->json($stats);
    }

    public function getFinancialStats(Request $request)
    {
        $dateRange = $this->getDateRange($request->get('period', 'month'));

        $stats = [
            'revenue_summary' => [
                'total_revenue' => DB::table('payment_details')->where('status', 'paid')->sum('amount'),
                'revenue_this_period' => DB::table('payment_details')
                    ->where('status', 'paid')
                    ->whereBetween('payment_date', [$dateRange['start'], $dateRange['end']])
                    ->sum('amount'),
                'pending_amount' => DB::table('payment_details')->where('status', 'pending')->sum('amount'),
            ],
            
            'late_payments' => DB::table('payment_installments')
                ->join('student_payment_plans', 'payment_installments.payment_plan_id', '=', 'student_payment_plans.id')
                ->join('students', 'student_payment_plans.student_id', '=', 'students.student_id')
                ->select('payment_installments.*', 'students.name_with_initials as student_name')
                ->where('payment_installments.status', 'overdue')->limit(20)->get(),
        ];

        return response()->json($stats);
    }

    public function getRecentActivities(Request $request)
    {
        $limit = $request->get('limit', 50);

        $recentStudents = DB::table('students')
            ->select('student_id as id', 'name_with_initials as title', 
                     DB::raw("'Student Registration' as type"), 'created_at',
                     DB::raw("CONCAT('New student: ', name_with_initials) as description"))
            ->orderBy('created_at', 'desc')->limit(10)->get();

        $recentCourseReg = DB::table('course_registration')
            ->join('students', 'course_registration.student_id', '=', 'students.student_id')
            ->join('courses', 'course_registration.course_id', '=', 'courses.course_id')
            ->select('course_registration.id', 'students.name_with_initials as title',
                     DB::raw("'Course Registration' as type"), 'course_registration.created_at',
                     DB::raw("CONCAT(students.name_with_initials, ' - ', courses.course_name) as description"))
            ->orderBy('course_registration.created_at', 'desc')->limit(10)->get();

        $activities = collect($recentStudents)->concat($recentCourseReg)
            ->sortByDesc('created_at')->take($limit)->values();

        return response()->json($activities);
    }

    public function getActionItems()
    {
        $actions = [
            'pending_registrations' => DB::table('course_registration')
                ->join('students', 'course_registration.student_id', '=', 'students.student_id')
                ->join('courses', 'course_registration.course_id', '=', 'courses.course_id')
                ->select('course_registration.*', 'students.name_with_initials as student_name', 'courses.course_name')
                ->where('course_registration.status', 'Pending')->limit(10)->get(),
            
            'pending_clearances' => DB::table('clearance_requests')
                ->join('students', 'clearance_requests.student_id', '=', 'students.student_id')
                ->select('clearance_requests.*', 'students.name_with_initials as student_name')
                ->where('clearance_requests.status', 'pending')->limit(10)->get(),
        ];

        return response()->json($actions);
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'today': return ['start' => Carbon::today(), 'end' => Carbon::tomorrow()];
            case 'week': return ['start' => Carbon::now()->startOfWeek(), 'end' => Carbon::now()->endOfWeek()];
            case 'month': return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()];
            case 'year': return ['start' => Carbon::now()->startOfYear(), 'end' => Carbon::now()->endOfYear()];
            default: return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()];
        }
    }
}