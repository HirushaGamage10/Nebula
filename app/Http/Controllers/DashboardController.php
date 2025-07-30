<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\Intake;
use App\Models\CourseRegistration;
use App\Models\StudentList;
use App\Models\StudentOtherInformation;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\StudentClearance;
use App\Models\Timetable;
use App\Models\ModuleManagement;
use Carbon\Carbon;
use App\Helpers\RoleHelper;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        $userRole = $user->user_role;
        
        // Get role-specific welcome message
        $welcomeMessage = $this->getWelcomeMessage($userRole);
        
        // Get role-specific permissions
        $permissions = RoleHelper::getRolePermissions($userRole);
        
        // Get available features for this role
        $availableFeatures = $this->getAvailableFeatures($userRole);
        
        return view('dashboard', compact('user', 'welcomeMessage', 'permissions', 'availableFeatures'));
    }
    
    private function getWelcomeMessage($role)
    {
        $messages = [
            'DGM' => 'Welcome Deputy General Manager! You have access to special approval features.',
            'Program Administrator (level 01)' => 'Welcome Program Administrator (level 01)! You can manage users, modules, courses, attendance, and clearances.',
            'Program Administrator (level 02)' => 'Welcome Program Administrator (level 02)! You can manage intakes, attendance, timetables, semesters, and exam results.',
            'Student Counselor' => 'Welcome Student Counselor! You can assist students with registration, payments, and eligibility.',
            'Librarian' => 'Welcome Librarian! You can manage library clearance processes.',
            'Hostel Manager' => 'Welcome Hostel Manager! You can manage hostel clearance processes.',
            'Bursar' => 'Welcome Bursar! You can manage financial and student records.',
            'Project Tutor' => 'Welcome Project Tutor! You can manage project clearance and attendance.',
            'Marketing Manager' => 'Welcome Marketing Manager! You can manage payment plans.',
            'Developer' => 'Welcome Developer! You have full system access to all features and functionalities.'
        ];
        
        return $messages[$role] ?? 'Welcome to the Nebula Institute Management System!';
    }
    
    private function getAvailableFeatures($role)
    {
        $features = [
            'DGM' => [
                'Student Management' => ['Student Registration', 'Course Registration', 'Eligibility & Registration', 'Student Information', 'Exam Results', 'Student Lists'],
                'Clearance Management' => ['All Clearance', 'Library Clearance', 'Hostel Clearance', 'Project Clearance'],
                'Academic Management' => ['Module Creation', 'Course Management', 'Intake Creation', 'Semester Creation', 'Module Management', 'Timetable'],
                'System Management' => ['User Management', 'Special Approvals', 'Attendance Management']
            ],
            'Manager' => [
                'Student Management' => ['Student Registration', 'Course Registration', 'Eligibility & Registration', 'Student Information', 'Exam Results', 'Student Lists'],
                'Clearance Management' => ['All Clearance', 'Library Clearance', 'Hostel Clearance', 'Project Clearance'],
                'Academic Management' => ['Module Creation', 'Course Management', 'Intake Creation', 'Semester Creation', 'Module Management', 'Timetable'],
                'System Management' => ['User Management', 'Attendance Management']
            ],
            'Program Administrator' => [
                'Student Management' => ['Student Registration', 'Course Registration', 'Eligibility & Registration', 'Student Information', 'Exam Results', 'Student Lists'],
                'Academic Management' => ['Module Creation', 'Course Management', 'Intake Creation', 'Semester Creation', 'Module Management', 'Timetable'],
                'System Management' => ['Attendance Management']
            ],
            'Student Counselor' => [
                'Student Management' => ['Student Registration', 'Course Registration', 'Eligibility & Registration', 'Student Information', 'Student Lists']
            ],
            'Librarian' => [
                'Clearance Management' => ['Library Clearance']
            ],
            'Hostel Manager' => [
                'Clearance Management' => ['Hostel Clearance']
            ],
            'Bursar' => [
                'Student Management' => ['Student Registration', 'Course Registration', 'Eligibility & Registration', 'Student Information', 'Exam Results', 'Student Lists'],
                'System Management' => ['Attendance Management']
            ],
            'Project Tutor' => [
                'Clearance Management' => ['Project Clearance'],
                'System Management' => ['Attendance Management']
            ],
            'Developer' => [
                'Student Management' => ['Student Registration', 'Course Registration', 'Eligibility & Registration', 'Student Information', 'Exam Results', 'Student Lists'],
                'Clearance Management' => ['All Clearance', 'Library Clearance', 'Hostel Clearance', 'Project Clearance'],
                'Academic Management' => ['Module Creation', 'Course Management', 'Intake Creation', 'Semester Creation', 'Module Management', 'Timetable'],
                'System Management' => ['User Management', 'Special Approvals', 'Attendance Management', 'File Management', 'Reporting', 'Data Export/Import', 'API Documentation'],
                'Financial Management' => ['Payment Plans', 'Payment Clearance'],
            ]
        ];
        
        return $features[$role] ?? [];
    }

    // API methods for charts
    public function getStudentsPerCourse()
    {
        $studentsPerCourse = CourseRegistration::with('course')
                                              ->selectRaw('course_id, COUNT(*) as count')
                                              ->groupBy('course_id')
                                              ->get()
                                              ->map(function($item) {
                                                  return [
                                                      'course_name' => $item->course->course_name ?? 'Unknown',
                                                      'count' => $item->count
                                                  ];
                                              });
        
        return response()->json($studentsPerCourse);
    }

    public function getCountrySurveyData()
    {
        // This would need to be implemented based on your marketing survey structure
        return response()->json([]);
    }

    public function getDropdownOptions()
    {
        $courses = Course::select('course_id', 'course_name')->get();
        $intakes = Intake::select('intake_id', 'intake_name')->get();
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        
        return response()->json([
            'courses' => $courses,
            'intakes' => $intakes,
            'locations' => $locations
        ]);
    }

    public function getRegistrationData()
    {
        $registrations = CourseRegistration::with(['student', 'course', 'intake'])
                                          ->latest()
                                          ->take(10)
                                          ->get();
        
        return response()->json($registrations);
    }

    public function getCourses()
    {
        $courses = Course::select('course_id', 'course_name')->get();
        return response()->json($courses);
    }
} 