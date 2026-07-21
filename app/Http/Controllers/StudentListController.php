<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Intake;
use App\Exports\StudentListExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class StudentListController extends Controller
{
    private function courseRegistrationHasSpecializationColumn(): bool
    {
        return Schema::hasColumn('course_registration', 'specialization');
    }

    private function normalizeSpecializationValue($value)
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function courseHasSpecializations(?Course $course): bool
    {
        if (!$course || empty($course->specializations)) {
            return false;
        }

        $specializations = is_array($course->specializations)
            ? $course->specializations
            : json_decode($course->specializations, true);

        return is_array($specializations) && count(array_filter($specializations)) > 0;
    }

    public function showStudentList()
    {
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        return view('student_management.student_list', compact('locations'));
    }

    /**
     * Fetch students joined with course_registration.
     */
    public function getStudentListData(Request $request)
    {
        $request->validate([
            'location'  => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'specialization' => 'nullable|string',
        ]);

        $location  = $request->location;
        $course_id = (int) $request->course_id;
        $intake_id = (int) $request->intake_id;
        $specialization = $this->normalizeSpecializationValue($request->specialization);
        $course = Course::find($course_id);
        $hasSpecializationColumn = $this->courseRegistrationHasSpecializationColumn();

        if ($this->courseHasSpecializations($course) && !$specialization) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a specialization for this course.'
            ], 422);
        }

        $students = DB::table('course_registration as cr')
            ->join('students as s', 's.student_id', '=', 'cr.student_id')
            ->where('cr.location', $location)
            ->where('cr.course_id', $course_id)
            ->where('cr.intake_id', $intake_id)
            ->when($specialization && $hasSpecializationColumn, function ($query) use ($specialization) {
                $query->where('cr.specialization', $specialization);
            })
            ->select([
                'cr.course_registration_id',
                's.student_id',
                DB::raw('COALESCE(s.name_with_initials, s.full_name) as name'),
                DB::raw($hasSpecializationColumn ? 'COALESCE(cr.specialization, "") as specialization' : '"" as specialization'),
                DB::raw('
                    CASE cr.status
                        WHEN "Pending" THEN "pending"
                        WHEN "Registered" THEN "registered"
                        WHEN "Not eligible" THEN "terminated"
                        WHEN "Completed" THEN "completed"
                        ELSE "pending"
                    END as status
                ')
            ])
            ->orderBy('s.name_with_initials')
            ->get();

        return response()->json([
            'success'  => true,
            'students' => $students,
        ]);
    }

    /**
     * Download student list as PDF.
     */
    public function downloadStudentList(Request $request)
    {
        $request->validate([
            'location'  => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'specialization' => 'nullable|string',
            'status'    => 'nullable|string|in:all,registered,terminated,completed,pending',
        ]);

        $location  = $request->location;
        $course_id = (int) $request->course_id;
        $intake_id = (int) $request->intake_id;
        $specialization = $this->normalizeSpecializationValue($request->specialization);
        $status    = $request->input('status', 'all');
        $course = Course::find($course_id);
        $hasSpecializationColumn = $this->courseRegistrationHasSpecializationColumn();

        if ($this->courseHasSpecializations($course) && !$specialization) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a specialization for this course.'
            ], 422);
        }

        $query = DB::table('course_registration as cr')
            ->join('students as s', 's.student_id', '=', 'cr.student_id')
            ->where('cr.location', $location)
            ->where('cr.course_id', $course_id)
            ->where('cr.intake_id', $intake_id);
        if ($specialization && $hasSpecializationColumn) {
            $query->where('cr.specialization', $specialization);
        }

        // status filter mapping
        if ($status !== 'all') {
            if ($status === 'registered') $query->where('cr.status', 'Registered');
            if ($status === 'terminated') $query->where('cr.status', 'Not eligible');
            if ($status === 'completed')  $query->where('cr.status', 'Completed');
            if ($status === 'pending')    $query->where('cr.status', 'Pending');
        }

        $students = $query->select([
                'cr.course_registration_id',
                's.student_id',
                DB::raw('COALESCE(s.name_with_initials, s.full_name) as name'),
            DB::raw($hasSpecializationColumn ? 'COALESCE(cr.specialization, "") as specialization' : '"" as specialization'),
                DB::raw('
                    CASE cr.status
                        WHEN "Pending" THEN "pending"
                        WHEN "Registered" THEN "registered"
                        WHEN "Not eligible" THEN "terminated"
                        WHEN "Completed" THEN "completed"
                        ELSE "pending"
                    END as status
                ')
            ])
            ->orderBy('s.name_with_initials')
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

        $pdf = Pdf::loadView('student_management.student_list_pdf', $data);
        return $pdf->download('student_list.pdf');
    }

    /**
     * Download as Excel file.
     */
    public function downloadStudentListExcel(Request $request)
    {
        $request->validate([
            'location'  => 'required|string',
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'specialization' => 'nullable|string',
            'status'    => 'nullable|string|in:all,registered,terminated,completed,pending',
        ]);

        $location  = $request->location;
        $course_id = (int) $request->course_id;
        $intake_id = (int) $request->intake_id;
        $specialization = $this->normalizeSpecializationValue($request->specialization);
        $status    = $request->input('status', 'all');
        $course = Course::find($course_id);
        $hasSpecializationColumn = $this->courseRegistrationHasSpecializationColumn();

        if ($this->courseHasSpecializations($course) && !$specialization) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a specialization for this course.'
            ], 422);
        }

        $query = DB::table('course_registration as cr')
            ->join('students as s', 's.student_id', '=', 'cr.student_id')
            ->where('cr.location', $location)
            ->where('cr.course_id', $course_id)
            ->where('cr.intake_id', $intake_id);
        if ($specialization && $hasSpecializationColumn) {
            $query->where('cr.specialization', $specialization);
        }

        // mapping
        if ($status !== 'all') {
            if ($status === 'registered') $query->where('cr.status', 'Registered');
            if ($status === 'terminated') $query->where('cr.status', 'Not eligible');
            if ($status === 'completed')  $query->where('cr.status', 'Completed');
            if ($status === 'pending')    $query->where('cr.status', 'Pending');
        }

        $students = $query->select([
                'cr.course_registration_id',
                's.student_id',
                DB::raw('COALESCE(s.name_with_initials, s.full_name) as name'),
            DB::raw($hasSpecializationColumn ? 'COALESCE(cr.specialization, "") as specialization' : '"" as specialization'),
                DB::raw('
                    CASE cr.status
                        WHEN "Pending" THEN "pending"
                        WHEN "Registered" THEN "registered"
                        WHEN "Not eligible" THEN "terminated"
                        WHEN "Completed" THEN "completed"
                        ELSE "pending"
                    END as status
                ')
            ])
            ->orderBy('s.name_with_initials')
            ->get();

        $course = Course::find($course_id);
        $intake = Intake::find($intake_id);

        // Excel
        $excelData = [];
        $counter = 1;
        foreach ($students as $s) {
            $excelData[] = [
                $counter++,
                $s->course_registration_id,
                $s->student_id,
                $s->name,
                $s->specialization ?: '-',
                ucfirst($s->status)
            ];
        }

        $filename = 'student_list_' . strtolower($status) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new StudentListExport($excelData, $course?->course_name ?? 'N/A', $location, $intake?->batch ?? 'N/A', $status),
            $filename
        );
    }

    /**
     * Get intakes for a course and location
     */
    public function getIntakesForCourseAndLocation($courseId, $location)
    {
        try {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['intakes' => []]);
            }

            $intakes = Intake::forCourse($course, $location)
                ->orderBy('batch')
                ->get(['intake_id', 'batch']);

            return response()->json(['intakes' => $intakes]);
        } catch (\Exception $e) {
            \Log::error('Error fetching intakes in StudentListController: ' . $e->getMessage());
            return response()->json(['intakes' => []], 500);
        }
    }
}
