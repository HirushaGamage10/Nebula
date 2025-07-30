<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\CourseRegistration;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentListController extends Controller
{
    // Method to show the student list view
    public function showStudentList()
    {
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        return view('student_list', compact('locations'));
    }

    public function getStudentListData(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
        ]);

        $students = CourseRegistration::where('location', $request->location)
            ->where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with('student')
            ->get()
            ->map(function($reg) {
                if ($reg->student) {
                    return [
                        'course_registration_id' => $reg->id,
                        'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                        'name_with_initials' => $reg->student->name_with_initials,
                    ];
                }
                return null;
            })
            ->filter(); 

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    // Download student list as PDF
    public function downloadStudentList(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
        ]);

        $students = CourseRegistration::where('location', $request->location)
            ->where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with(['student', 'course', 'intake'])
            ->get();

        // Get course and intake details
        $course = Course::find($request->course_id);
        $intake = Intake::find($request->intake_id);

        // Get location text
        $locationText = 'Nebula Institute of Technology - ' . $request->location;
        $courseText = $course ? $course->course_name : 'N/A';
        $intakeText = $intake ? $intake->batch : 'N/A';

        $data = [
            'students' => $students,
            'location' => $request->location,
            'locationText' => $locationText,
            'course' => $course,
            'courseText' => $courseText,
            'intake' => $intake,
            'intakeText' => $intakeText,
            'total_count' => $students->count(),
            'isPdf' => true // Flag to indicate this is for PDF generation
        ];

        $pdf = PDF::loadView('student_list_pdf', $data);
        
        return $pdf->download('student_list.pdf');
    }
}
