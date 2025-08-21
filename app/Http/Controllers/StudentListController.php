<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Intake;
use App\Exports\StudentListExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class StudentListController extends Controller
{
    public function showStudentList()
    {
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        return view('student_list', compact('locations'));
    }

    /**
     * Return ALL students for the intake from semester_registrations
     * (the UI can filter by tab). Includes status + course_registration_id.
     */
    public function getStudentListData(Request $request)
    {
        $request->validate([
            'location'  => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
        ]);

        $location  = $request->location;
        $course_id = (int) $request->course_id;
        $intake_id = (int) $request->intake_id;

        $rows = DB::table('semester_registrations as sr')
            ->join('students as s', 's.student_id', '=', 'sr.student_id')
            ->leftJoin('course_registration as cr', function ($j) use ($intake_id, $course_id, $location) {
                $j->on('cr.student_id', '=', 'sr.student_id')
                  ->where('cr.intake_id', $intake_id)
                  ->where('cr.course_id', $course_id)
                  ->where('cr.location', $location);
            })
            ->where('sr.intake_id', $intake_id)
            ->where('sr.course_id', $course_id)
            ->where('sr.location', $location)
            ->select([
                'sr.student_id',
                'sr.status', // registered | terminated | ...
                DB::raw('COALESCE(cr.course_registration_id, "") as course_registration_id'),
                DB::raw('COALESCE(s.name_with_initials, s.full_name) as name'),
            ])
            ->orderByRaw('COALESCE(s.name_with_initials, s.full_name)')
            ->get();

        return response()->json([
            'success'  => true,
            'students' => $rows,
        ]);
    }

    /**
     * Download the list as PDF.
     * Optional status filter: all | registered | terminated
     */
    public function downloadStudentList(Request $request)
    {
        $request->validate([
            'location'  => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'status'    => 'nullable|string|in:all,registered,terminated',
        ]);

        $location  = $request->location;
        $course_id = (int) $request->course_id;
        $intake_id = (int) $request->intake_id;
        $status    = $request->input('status', 'all');

        $query = DB::table('semester_registrations as sr')
            ->join('students as s', 's.student_id', '=', 'sr.student_id')
            ->leftJoin('course_registration as cr', function ($j) use ($intake_id, $course_id, $location) {
                $j->on('cr.student_id', '=', 'sr.student_id')
                  ->where('cr.intake_id', $intake_id)
                  ->where('cr.course_id', $course_id)
                  ->where('cr.location', $location);
            })
            ->where('sr.intake_id', $intake_id)
            ->where('sr.course_id', $course_id)
            ->where('sr.location', $location);

        if ($status !== 'all') {
            $query->where('sr.status', $status);
        }

        $students = $query->select([
                'sr.student_id',
                'sr.status',
                DB::raw('COALESCE(cr.course_registration_id, "") as course_registration_id'),
                DB::raw('COALESCE(s.name_with_initials, s.full_name) as name'),
            ])
            ->orderByRaw('COALESCE(s.name_with_initials, s.full_name)')
            ->get();

        $course = Course::find($course_id);
        $intake = Intake::find($intake_id);

        $data = [
            'students'     => $students,
            'locationText' => 'Nebula Institute of Technology - ' . $location,
            'courseText'   => $course?->course_name ?? 'N/A',
            'intakeText'   => $intake?->batch ?? 'N/A',
            'total_count'  => $students->count(),
            'status'       => $status,
        ];

        $pdf = Pdf::loadView('student_list_pdf', $data);
        return $pdf->download('student_list.pdf');
    }

    /**
     * Download the list as Excel.
     * Optional status filter: all | registered | terminated
     */
    public function downloadStudentListExcel(Request $request)
    {
        $request->validate([
            'location'  => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'status'    => 'nullable|string|in:all,registered,terminated',
        ]);

        $location  = $request->location;
        $course_id = (int) $request->course_id;
        $intake_id = (int) $request->intake_id;
        $status    = $request->input('status', 'all');

        $query = DB::table('semester_registrations as sr')
            ->join('students as s', 's.student_id', '=', 'sr.student_id')
            ->leftJoin('course_registration as cr', function ($j) use ($intake_id, $course_id, $location) {
                $j->on('cr.student_id', '=', 'sr.student_id')
                  ->where('cr.intake_id', $intake_id)
                  ->where('cr.course_id', $course_id)
                  ->where('cr.location', $location);
            })
            ->where('sr.intake_id', $intake_id)
            ->where('sr.course_id', $course_id)
            ->where('sr.location', $location);

        if ($status !== 'all') {
            $query->where('sr.status', $status);
        }

        $students = $query->select([
                'sr.student_id',
                'sr.status',
                DB::raw('COALESCE(cr.course_registration_id, "") as course_registration_id'),
                DB::raw('COALESCE(s.name_with_initials, s.full_name) as name'),
            ])
            ->orderByRaw('COALESCE(s.name_with_initials, s.full_name)')
            ->get();

        $course = Course::find($course_id);
        $intake = Intake::find($intake_id);

        // Prepare data for Excel
        $excelData = [];
        $counter = 1;
        foreach ($students as $student) {
            $excelData[] = [
                $counter++,
                $student->course_registration_id,
                $student->student_id,
                $student->name,
                ucfirst($student->status)
            ];
        }

        // Generate filename
        $filename = 'student_list_' . strtolower($status) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Return Excel file as download
        return Excel::download(
            new StudentListExport($excelData, $course?->course_name ?? 'N/A', $location, $intake?->batch ?? 'N/A', $status),
            $filename
        );
    }
}
