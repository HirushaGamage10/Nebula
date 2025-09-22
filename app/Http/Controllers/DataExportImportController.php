<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Module;
use App\Models\ModuleManagement;
use App\Models\Intake;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataExportImportController extends Controller
{
    /**
     * Show the data export/import dashboard
     */
    public function showDashboard()
    {
        if (!Auth::check() || !Auth::user()->status) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return view('data-export-import.dashboard');
    }

    /**
     * Export students data
     */
    public function exportStudents(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'format' => 'required|string|in:csv,excel,json',
                'filters' => 'nullable|array'
            ]);

            $query = Student::query();

            // Apply filters
            if ($request->filled('filters.location')) {
                $query->where('institute_location', $request->input('filters.location'));
            }
            if ($request->filled('filters.gender')) {
                $query->where('gender', $request->input('filters.gender'));
            }
            if ($request->filled('filters.start_date')) {
                $query->whereDate('created_at', '>=', $request->input('filters.start_date'));
            }
            if ($request->filled('filters.end_date')) {
                $query->whereDate('created_at', '<=', $request->input('filters.end_date'));
            }

            $students = $query->get();

            $format = $request->input('format');
            $filename = "students_export_" . now()->format('Y-m-d_H-i-s') . ".{$format}";

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($students, $filename, 'students');
                case 'excel':
                    return $this->exportToExcel($students, $filename, 'students');
                case 'json':
                    return $this->exportToJSON($students, $filename, 'students');
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid format.'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Student export failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed.'
            ], 500);
        }
    }

    /**
     * Export courses data
     */
    public function exportCourses(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'format' => 'required|string|in:csv,excel,json'
            ]);

            $courses = Course::all();
            $format = $request->input('format');
            $filename = "courses_export_" . now()->format('Y-m-d_H-i-s') . ".{$format}";

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($courses, $filename, 'courses');
                case 'excel':
                    return $this->exportToExcel($courses, $filename, 'courses');
                case 'json':
                    return $this->exportToJSON($courses, $filename, 'courses');
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid format.'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Course export failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed.'
            ], 500);
        }
    }

    /**
     * Export attendance data
     */
    public function exportAttendance(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'format' => 'required|string|in:csv,excel,json',
                'filters' => 'nullable|array'
            ]);

            $query = Attendance::with(['student', 'course']);

            // Apply filters
            if ($request->filled('filters.start_date')) {
                $query->whereDate('date', '>=', $request->input('filters.start_date'));
            }
            if ($request->filled('filters.end_date')) {
                $query->whereDate('date', '<=', $request->input('filters.end_date'));
            }
            if ($request->filled('filters.course_id')) {
                $query->where('course_id', $request->input('filters.course_id'));
            }
            if ($request->filled('filters.status')) {
                $query->where('status', $request->input('filters.status'));
            }

            $attendance = $query->get();
            $format = $request->input('format');
            $filename = "attendance_export_" . now()->format('Y-m-d_H-i-s') . ".{$format}";

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($attendance, $filename, 'attendance');
                case 'excel':
                    return $this->exportToExcel($attendance, $filename, 'attendance');
                case 'json':
                    return $this->exportToJSON($attendance, $filename, 'attendance');
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid format.'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Attendance export failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed.'
            ], 500);
        }
    }

    /**
     * Export exam results data
     */
    public function exportExamResults(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'format' => 'required|string|in:csv,excel,json',
                'filters' => 'nullable|array'
            ]);

            $query = ExamResult::with(['student', 'course']);

            // Apply filters
            if ($request->filled('filters.course_id')) {
                $query->where('course_id', $request->input('filters.course_id'));
            }
            if ($request->filled('filters.exam_type')) {
                $query->where('exam_type', $request->input('filters.exam_type'));
            }
            if ($request->filled('filters.start_date')) {
                $query->whereDate('exam_date', '>=', $request->input('filters.start_date'));
            }
            if ($request->filled('filters.end_date')) {
                $query->whereDate('exam_date', '<=', $request->input('filters.end_date'));
            }

            $examResults = $query->get();
            $format = $request->input('format');
            $filename = "exam_results_export_" . now()->format('Y-m-d_H-i-s') . ".{$format}";

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($examResults, $filename, 'exam_results');
                case 'excel':
                    return $this->exportToExcel($examResults, $filename, 'exam_results');
                case 'json':
                    return $this->exportToJSON($examResults, $filename, 'exam_results');
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid format.'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Exam results export failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed.'
            ], 500);
        }
    }

    /**
     * Import students data
     */
    public function importStudents(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls',
                'format' => 'required|string|in:csv,excel'
            ]);

            $file = $request->file('file');
            $format = $request->input('format');

            $importedCount = 0;
            $errors = [];

            switch ($format) {
                case 'csv':
                    $result = $this->importFromCSV($file, 'students');
                    break;
                case 'excel':
                    $result = $this->importFromExcel($file, 'students');
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid format.'], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully.',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Student import failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import exam results data
     */
    public function importExamResults(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx,xls',
                'format' => 'required|string|in:csv,excel'
            ]);

            $file = $request->file('file');
            $format = $request->input('format');

            switch ($format) {
                case 'csv':
                    $result = $this->importFromCSV($file, 'exam_results');
                    break;
                case 'excel':
                    $result = $this->importFromExcel($file, 'exam_results');
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid format.'], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Exam results import completed successfully.',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Exam results import failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import template
     */
    public function getImportTemplate(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $request->validate([
                'type' => 'required|string|in:students,courses,attendance,exam_results',
                'format' => 'required|string|in:csv,excel'
            ]);

            $type = $request->input('type');
            $format = $request->input('format');
            $filename = "{$type}_template.{$format}";

            return $this->generateTemplate($type, $filename, $format);

        } catch (\Exception $e) {
            Log::error('Template generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Template generation failed.'
            ], 500);
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($data, $filename, $type)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Write headers based on type
            switch ($type) {
                case 'students':
                    fputcsv($file, [
                        'Student ID', 'Full Name', 'Email', 'Phone', 'NIC', 'Gender',
                        'Date of Birth', 'Address', 'Institute Location', 'Created At'
                    ]);
                    break;
                case 'courses':
                    fputcsv($file, [
                        'Course ID', 'Course Name', 'Duration', 'Fee', 'Description', 'Created At'
                    ]);
                    break;
                case 'attendance':
                    fputcsv($file, [
                        'Attendance ID', 'Student ID', 'Student Name', 'Course ID', 'Course Name',
                        'Date', 'Status', 'Remarks', 'Created At'
                    ]);
                    break;
                case 'exam_results':
                    fputcsv($file, [
                        'Result ID', 'Student ID', 'Student Name', 'Course ID', 'Course Name',
                        'Exam Type', 'Score', 'Max Score', 'Exam Date', 'Remarks', 'Created At'
                    ]);
                    break;
            }

            // Write data
            foreach ($data as $row) {
                switch ($type) {
                    case 'students':
                        fputcsv($file, [
                            $row->student_id,
                            $row->full_name,
                            $row->email,
                            $row->phone,
                            $row->nic,
                            $row->gender,
                            $row->date_of_birth,
                            $row->address,
                            $row->institute_location,
                            $row->created_at
                        ]);
                        break;
                    case 'courses':
                        fputcsv($file, [
                            $row->course_id,
                            $row->course_name,
                            $row->duration,
                            $row->fee,
                            $row->description,
                            $row->created_at
                        ]);
                        break;
                    case 'attendance':
                        fputcsv($file, [
                            $row->attendance_id,
                            $row->student_id,
                            $row->student->full_name ?? 'N/A',
                            $row->course_id,
                            $row->course->course_name ?? 'N/A',
                            $row->date,
                            $row->status,
                            $row->remarks,
                            $row->created_at
                        ]);
                        break;
                    case 'exam_results':
                        fputcsv($file, [
                            $row->result_id,
                            $row->student_id,
                            $row->student->full_name ?? 'N/A',
                            $row->course_id,
                            $row->course->course_name ?? 'N/A',
                            $row->exam_type,
                            $row->score,
                            $row->max_score,
                            $row->exam_date,
                            $row->remarks,
                            $row->created_at
                        ]);
                        break;
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($data, $filename, $type)
    {
        // For now, return CSV format as Excel
        // In a real implementation, you would use a library like PhpSpreadsheet
        return $this->exportToCSV($data, str_replace('.xlsx', '.csv', $filename), $type);
    }

    /**
     * Export to JSON
     */
    private function exportToJSON($data, $filename, $type)
    {
        $jsonData = $data->toArray();
        
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->json($jsonData, 200, $headers);
    }

    /**
     * Import from CSV
     */
    private function importFromCSV($file, $type)
    {
        $importedCount = 0;
        $errors = [];
        $rowNumber = 1;

        if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
            // Skip header row
            fgetcsv($handle);
            $rowNumber++;

            while (($data = fgetcsv($handle)) !== FALSE) {
                try {
                    switch ($type) {
                        case 'students':
                            $this->importStudent($data);
                            break;
                        case 'exam_results':
                            $this->importExamResult($data);
                            break;
                        // Add other types as needed
                    }
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                        'data' => $data
                    ];
                }
                $rowNumber++;
            }
            fclose($handle);
        }

        return [
            'imported_count' => $importedCount,
            'errors' => $errors,
            'total_rows' => $rowNumber - 1
        ];
    }

    /**
     * Import from Excel
     */
    private function importFromExcel($file, $type)
    {
        // For now, treat as CSV
        // In a real implementation, you would use a library like PhpSpreadsheet
        return $this->importFromCSV($file, $type);
    }

    /**
     * Import student data
     */
    private function importStudent($data)
    {
        // Validate required fields
        if (empty($data[1]) || empty($data[2]) || empty($data[3])) {
            throw new \Exception('Required fields missing: Full Name, Email, Phone');
        }

        // Check if student already exists
        $existingStudent = Student::where('email', $data[2])->first();
        if ($existingStudent) {
            throw new \Exception('Student with this email already exists');
        }

        // Create student
        Student::create([
            'full_name' => $data[1],
            'email' => $data[2],
            'phone' => $data[3],
            'nic' => $data[4] ?? null,
            'gender' => $data[5] ?? 'Male',
            'date_of_birth' => $data[6] ?? null,
            'address' => $data[7] ?? null,
            'institute_location' => $data[8] ?? 'Moratuwa',
            'status' => true
        ]);
    }

    /**
     * Import exam result data
     */
    private function importExamResult($data)
    {
        // Validate required fields
        // CSV format: Student ID, Course ID, Module ID, Intake ID, Location, Semester, Marks, Grade, Remarks
        if (empty($data[0]) || empty($data[1]) || empty($data[2]) || empty($data[3])) {
            throw new \Exception('Required fields missing: Student ID, Course ID, Module ID, Intake ID');
        }

        // Validate that student exists
        $student = Student::where('student_id', $data[0])->first();
        if (!$student) {
            throw new \Exception("Student with ID {$data[0]} not found");
        }

        // Validate that course exists
        $course = Course::where('course_id', $data[1])->first();
        if (!$course) {
            throw new \Exception("Course with ID {$data[1]} not found");
        }

        // Validate that module exists
        $module = Module::where('module_id', $data[2])->first();
        if (!$module) {
            throw new \Exception("Module with ID {$data[2]} not found");
        }

        // Validate that intake exists
        $intake = Intake::where('intake_id', $data[3])->first();
        if (!$intake) {
            throw new \Exception("Intake with ID {$data[3]} not found");
        }

        // Check if exam result already exists for this student, course, module, and intake
        $existingResult = ExamResult::where('student_id', $data[0])
            ->where('course_id', $data[1])
            ->where('module_id', $data[2])
            ->where('intake_id', $data[3])
            ->first();
        
        if ($existingResult) {
            throw new \Exception("Exam result already exists for Student ID {$data[0]}, Course ID {$data[1]}, Module ID {$data[2]}, Intake ID {$data[3]}");
        }

        // Validate marks (if provided)
        $marks = null;
        if (!empty($data[6])) {
            $marks = (int) $data[6];
            if ($marks < 0 || $marks > 100) {
                throw new \Exception('Marks must be between 0 and 100');
            }
        }

        // Auto-calculate grade if marks provided but grade not provided
        $grade = $data[7] ?? null;
        if ($marks !== null && empty($grade)) {
            $grade = ExamResult::calculateGradeFromMarks($marks);
        }

        // Validate location
        $location = $data[4] ?? 'Moratuwa';
        if (!in_array($location, ['Welisara', 'Moratuwa', 'Peradeniya'])) {
            throw new \Exception('Invalid location. Must be one of: Welisara, Moratuwa, Peradeniya');
        }

        // Validate semester
        $semester = $data[5] ?? 1;
        if (!is_numeric($semester) || $semester < 1 || $semester > 8) {
            throw new \Exception('Semester must be a number between 1 and 8');
        }

        // Create exam result
        ExamResult::create([
            'student_id' => $data[0],
            'course_id' => $data[1],
            'module_id' => $data[2],
            'intake_id' => $data[3],
            'location' => $location,
            'semester' => (int) $semester,
            'marks' => $marks,
            'grade' => $grade,
            'remarks' => $data[8] ?? null
        ]);
    }

    /**
     * Generate template
     */
    private function generateTemplate($type, $filename, $format)
    {
        $headers = [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'students':
                    fputcsv($file, [
                        'Student ID', 'Full Name', 'Email', 'Phone', 'NIC', 'Gender',
                        'Date of Birth', 'Address', 'Institute Location'
                    ]);
                    // Add sample row
                    fputcsv($file, [
                        '', 'John Doe', 'john@example.com', '+94712345678', '123456789V',
                        'Male', '1990-01-01', '123 Main St, Colombo', 'Moratuwa'
                    ]);
                    break;
                case 'courses':
                    fputcsv($file, [
                        'Course ID', 'Course Name', 'Duration', 'Fee', 'Description'
                    ]);
                    fputcsv($file, [
                        '', 'Computer Science', '3 years', '50000', 'Bachelor of Computer Science'
                    ]);
                    break;
                case 'attendance':
                    fputcsv($file, [
                        'Student ID', 'Course ID', 'Date', 'Status', 'Remarks'
                    ]);
                    fputcsv($file, [
                        '1', '1', '2024-01-15', 'Present', 'Good participation'
                    ]);
                    break;
                case 'exam_results':
                    fputcsv($file, [
                        'Student ID', 'Course ID', 'Module ID', 'Intake ID', 'Location', 'Semester', 'Marks', 'Grade', 'Remarks'
                    ]);
                    fputcsv($file, [
                        '1', '1', '1', '1', 'Moratuwa', '1', '85', 'B', 'Good performance'
                    ]);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get export statistics
     */
    public function getExportStats()
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $stats = [
                'students' => [
                    'total' => Student::count(),
                    'active' => Student::where('status', true)->count(),
                    'by_location' => Student::select('institute_location', DB::raw('count(*) as count'))
                        ->groupBy('institute_location')
                        ->get()
                ],
                'courses' => [
                    'total' => Course::count(),
                    'active' => Course::count() // Assuming all courses are active
                ],
                'attendance' => [
                    'total_records' => Attendance::count(),
                    'present_count' => Attendance::where('status', 'Present')->count(),
                    'absent_count' => Attendance::where('status', 'Absent')->count(),
                    'late_count' => Attendance::where('status', 'Late')->count()
                ],
                'exam_results' => [
                    'total_records' => ExamResult::count(),
                    'average_score' => ExamResult::avg('score'),
                    'highest_score' => ExamResult::max('score'),
                    'lowest_score' => ExamResult::min('score')
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Export stats failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get export statistics.'
            ], 500);
        }
    }
}
