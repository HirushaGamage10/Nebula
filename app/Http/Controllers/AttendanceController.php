<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Exports\AttendanceExport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Excel as MaatExcel;

class AttendanceController extends Controller
{
    public function index()
    {
        $courses = Course::all(['course_id', 'course_name']);
        $intakes = Intake::all(['intake_id', 'batch']);
        
        return view('attendance', compact('courses', 'intakes'));
    }

    public function getCoursesByLocation(Request $request)
    {
        $location = $request->query('location');
        $courseType = $request->query('course_type');

        if (!$location || !$courseType) {
            return response()->json(['success' => false, 'message' => 'Location and Course Type are required.']);
        }
        try {
            $courses = Course::select('course_id', 'course_name')
                ->where('location', $location)
                ->where('course_type', $courseType)
                ->orderBy('course_name', 'asc')
                ->get();

            if ($courses->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No courses found for this location and type.']);
            }

            return response()->json(['success' => true, 'courses' => $courses]);
        } catch (\Exception $e) {
            Log::error('Error fetching courses by location: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching courses.'], 500);
        }
    }

    public function getIntakesForCourseAndLocation(Request $request, $courseId, $location)
    {
        try {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['error' => 'Course not found.'], 404);
            }

            $intakes = Intake::where('course_name', $course->course_name)
                            ->where('location', $location)
                            ->orderBy('batch')
                            ->get(['intake_id', 'batch']);

            return response()->json(['intakes' => $intakes]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function getSemesters(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
        ]);

        $course = Course::find($request->course_id);
        $intake = Intake::find($request->intake_id);

        if (!$course || !$intake) {
            return response()->json(['error' => 'Invalid course or intake.'], 404);
        }

        $semesters = \App\Models\Semester::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->whereIn('status', ['active', 'upcoming'])
            ->get(['id as semester_id', 'name as semester_name']);
            
        return response()->json(['semesters' => $semesters]);
    }

    public function getFilteredModules(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'semester' => 'required|integer',
            'location' => 'required|string',
        ]);

        $courseId = $request->input('course_id');
        $semesterId = $request->input('semester');

        // Get the semester by ID
        $semester = \App\Models\Semester::where('course_id', $courseId)
            ->where('intake_id', $request->input('intake_id'))
            ->where('id', $semesterId)
            ->first();

        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Filter modules by semester using the semester_module table
        $modules = \App\Models\Module::join('semester_module', 'modules.module_id', '=', 'semester_module.module_id')
            ->where('semester_module.semester_id', $semester->id)
            ->select('modules.module_id', 'modules.module_name')
            ->get();

        return response()->json(['modules' => $modules]);
    }

    public function getStudentsForAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required',
            'module_id' => 'required|exists:modules,module_id',
        ]);

        $courseId = $request->course_id;
        $intakeId = $request->intake_id;
        $location = $request->location;
        $semesterId = $request->semester;
        $moduleId = $request->module_id;

        // Get the semester to determine if it's core or elective
        $semester = \App\Models\Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Check if this is a core module (assigned to semester) or elective module
        $isCoreModule = DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->where('module_id', $moduleId)
            ->exists();

        if ($isCoreModule) {
            // For core modules: Get students registered for the semester
            $students = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('status', 'registered')
                ->with('student')
                ->get()
                ->map(function($reg) {
                    return [
                        'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                        'student_id' => $reg->student->student_id,
                        'name_with_initials' => $reg->student->name_with_initials,
                    ];
                });
        } else {
            // For elective modules: Get students registered for the specific module
            $students = \App\Models\ModuleManagement::where('module_id', $moduleId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->with('student')
                ->get()
                ->map(function($reg) {
                    return [
                        'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                        'student_id' => $reg->student->student_id,
                        'name_with_initials' => $reg->student->name_with_initials,
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'semester' => 'required',
            'module_id' => 'required|integer',
            'date' => 'required|date',
            'attendance_data' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

            $date = Carbon::parse($request->date);
            
            // Get the semester to convert ID to name
            $semester = \App\Models\Semester::find($request->semester);
            if (!$semester) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semester not found.'
                ], 404);
            }
            
            // Delete existing attendance records for this date, course, intake, semester, and module
            Attendance::where('date', $date)
                     ->where('course_id', $request->course_id)
                     ->where('intake_id', $request->intake_id)
                     ->where('semester', $semester->name)
                     ->where('module_id', $request->module_id)
                     ->delete();

            // Insert new attendance records
            $attendanceRecords = [];
            foreach ($request->attendance_data as $studentData) {
                if (!isset($studentData['student_id'])) {
                    continue; // Skip invalid records
                }
                
                $attendanceRecords[] = [
                    'location' => $request->location,
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'semester' => $semester->name,
                    'module_id' => $request->module_id,
                    'date' => $date,
                    'student_id' => $studentData['student_id'],
                    'status' => $studentData['status'] ?? false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (empty($attendanceRecords)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid attendance data provided.'
                ], 400);
            }

            Attendance::insert($attendanceRecords);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance saved successfully for ' . count($attendanceRecords) . ' students.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Attendance save error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendanceHistory(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'semester' => 'required',
            'module_id' => 'required|integer',
            'date' => 'required|date'
        ]);

        try {
            // Get the semester to convert ID to name
            $semester = \App\Models\Semester::find($request->semester);
            if (!$semester) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semester not found.'
                ], 404);
            }

            $attendance = Attendance::where('location', $request->location)
                                   ->where('course_id', $request->course_id)
                                   ->where('intake_id', $request->intake_id)
                                   ->where('semester', $semester->name)
                                   ->where('module_id', $request->module_id)
                                   ->where('date', $request->date)
                                   ->with('student')
                                   ->get();

            return response()->json([
                'success' => true,
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance history error: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance history: ' . $e->getMessage()
            ], 500);
        }
    }

    // Debug method to check database data
    public function debugData()
    {
        $courses = Course::all(['course_id', 'course_name', 'location']);
        $intakes = Intake::all(['intake_id', 'course_name', 'location', 'batch']);
        $courseTypes = Course::select('course_type')->distinct()->get();
        
        return response()->json([
            'distinct_course_types' => $courseTypes,
            'courses' => $courses,
            'intakes' => $intakes,
            'message' => 'Check the browser console for detailed data'
        ]);
    }

    public function getOverallAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required',
            'module_id' => 'required|exists:modules,module_id',
        ]);

        $courseId = $request->course_id;
        $intakeId = $request->intake_id;
        $location = $request->location;
        $semesterId = $request->semester;
        $moduleId = $request->module_id;

        // Get the semester to determine if it's core or elective
        $semester = \App\Models\Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Check if this is a core module (assigned to semester) or elective module
        $isCoreModule = DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->where('module_id', $moduleId)
            ->exists();

        // Get all attendance sessions for this filter (by module)
        $attendanceSessions = \App\Models\Attendance::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->where('location', $location)
            ->where('semester', $semester->name)
            ->where('module_id', $moduleId)
            ->select('date')
            ->distinct()
            ->get();
        $totalSessions = $attendanceSessions->count();

        if ($isCoreModule) {
            // For core modules: Get students registered for the semester
            $registrations = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('status', 'registered')
                ->with('student')
                ->get();
        } else {
            // For elective modules: Get students registered for the specific module
            $registrations = \App\Models\ModuleManagement::where('module_id', $moduleId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->with('student')
                ->get();
        }

        $attendanceData = [];
        foreach ($registrations as $reg) {
            $attendedSessions = \App\Models\Attendance::where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->where('module_id', $moduleId)
                ->where('student_id', $reg->student_id)
                ->where('status', true)
                ->count();
            $attendanceData[] = [
                'registration_number' => $reg->student->registration_id ?? $reg->student->student_id,
                'name_with_initials' => $reg->student->name_with_initials,
                'total_sessions' => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'percentage' => $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) : 0
            ];
        }
        return response()->json([
            'success' => true,
            'attendance' => $attendanceData
        ]);
    }

    /**
     * Download attendance report as Excel.
     */
    public function downloadAttendanceExcel(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required',
            'module_id' => 'required|exists:modules,module_id',
        ]);

        $courseId = $request->course_id;
        $intakeId = $request->intake_id;
        $location = $request->location;
        $semesterId = $request->semester;
        $moduleId = $request->module_id;

        // Get the semester to determine if it's core or elective
        $semester = \App\Models\Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found.'], 404);
        }

        // Check if this is a core module (assigned to semester) or elective module
        $isCoreModule = DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->where('module_id', $moduleId)
            ->exists();

        // Get all attendance sessions for this filter (by module)
        $attendanceSessions = \App\Models\Attendance::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->where('location', $location)
            ->where('semester', $semester->name)
            ->where('module_id', $moduleId)
            ->select('date')
            ->distinct()
            ->get();
        $totalSessions = $attendanceSessions->count();

        if ($isCoreModule) {
            // For core modules: Get students registered for the semester
            $registrations = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('status', 'registered')
                ->with('student')
                ->get();
        } else {
            // For elective modules: Get students registered for the specific module
            $registrations = \App\Models\ModuleManagement::where('module_id', $moduleId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->with('student')
                ->get();
        }

        $excelData = [];
        foreach ($registrations as $reg) {
            $attendedSessions = \App\Models\Attendance::where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('semester', $semester->name)
                ->where('module_id', $moduleId)
                ->where('student_id', $reg->student_id)
                ->where('status', true)
                ->count();
            
            $excelData[] = [
                $reg->student->registration_id ?? $reg->student->student_id,
                $reg->student->name_with_initials,
                $totalSessions,
                $attendedSessions,
                $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) . '%' : '0%'
            ];
        }

        // Get course, intake, and module details for filename
        $course = Course::find($courseId);
        $intake = Intake::find($intakeId);
        $module = Module::find($moduleId);

        // Generate filename
        $filename = 'attendance_report_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Return Excel file as download
        return Excel::download(
            new AttendanceExport($excelData, $location, $course?->course_name ?? 'N/A', $intake?->batch ?? 'N/A', $semester->name, $module?->module_name ?? 'N/A'),
            $filename
        );
    }

    /**
     * Download a template file for bulk attendance import.
     * Columns: registration_number, name_with_initials, attendance (Present/Absent)
     */
    public function downloadTemplate(Request $request)
    {
        // Build an XLSX template with optional prefilled students if filters provided
        $location = $request->query('location');
        $courseId = $request->query('course_id');
        $intakeId = $request->query('intake_id');
        $semesterId = $request->query('semester');
        $moduleId = $request->query('module_id');
        $date = $request->query('date');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Attendance Template');

        // Header
        $sheet->fromArray(['registration_number', 'name_with_initials', 'attendance'], null, 'A1');

        // If filters provided and module + date present, attempt to prefill students
        $startRow = 2;
        if ($courseId && $intakeId && $moduleId) {
            // fetch students for the module using existing logic
            try {
                $students = collect();
                $semester = null;
                if ($semesterId) {
                    $semester = \App\Models\Semester::find($semesterId);
                }

                // detect if core module
                if ($semester) {
                    $isCore = DB::table('semester_module')->where('semester_id', $semesterId)->where('module_id', $moduleId)->exists();
                } else {
                    $isCore = DB::table('semester_module')->where('module_id', $moduleId)->exists();
                }

                if ($isCore && $semester) {
                    $regs = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                        ->where('course_id', $courseId)
                        ->where('intake_id', $intakeId)
                        ->where('location', $location)
                        ->where('status', 'registered')
                        ->with('student')
                        ->get();

                    foreach ($regs as $r) {
                        $students->push([$r->student->registration_id ?? $r->student->student_id, $r->student->name_with_initials]);
                    }
                } else {
                    $mods = \App\Models\ModuleManagement::where('module_id', $moduleId)
                        ->where('course_id', $courseId)
                        ->where('intake_id', $intakeId)
                        ->where('location', $location)
                        ->when($semester, function($q) use ($semester) { return $q->where('semester', $semester->name); })
                        ->with('student')
                        ->get();
                    foreach ($mods as $m) {
                        $students->push([$m->student->registration_id ?? $m->student->student_id, $m->student->name_with_initials]);
                    }
                }

                if ($students->isNotEmpty()) {
                    $sheet->fromArray($students->toArray(), null, 'A' . $startRow);
                }
            } catch (\Exception $e) {
                // ignore prefill errors
                Log::warning('Failed to prefill attendance template: ' . $e->getMessage());
            }
        }

        // Add data validation dropdown for the attendance column (column C)
        $highestRow = max($sheet->getHighestRow(), 100); // create at least some rows to use
        for ($row = 2; $row <= $highestRow; $row++) {
            $validation = $sheet->getCell('C' . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid value');
            $validation->setError('Value must be Present or Absent');
            $validation->setPromptTitle('Select attendance');
            $validation->setPrompt('Choose Present or Absent from the dropdown');
            $validation->setFormula1('"Present,Absent"');
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'attendance_import_template_' . date('Y-m-d') . '.xlsx';
        // Stream to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Import attendance from uploaded CSV/Excel file.
     * Expects selected filters (location, course_id, intake_id, semester, module_id, date) to be present
     * and file under 'attendance_file'.
     */
    public function importAttendance(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'semester' => 'required',
            'module_id' => 'required|integer',
            'date' => 'required|date',
            'attendance_file' => 'required|file'
        ]);

        $file = $request->file('attendance_file');
        $ext = strtolower($file->getClientOriginalExtension());

        $rows = [];
        try {
            if (in_array($ext, ['xlsx', 'xls'])) {
                // use maatwebsite/excel to get as array
                $array = Excel::toArray([], $file);
                if (is_array($array) && isset($array[0]) && count($array[0]) > 0) {
                    $sheet = $array[0];
                    $header = array_map('trim', $sheet[0]);
                    for ($i = 1; $i < count($sheet); $i++) {
                        $row = $sheet[$i];
                        if (count($row) === 0) continue;
                        // pad row to header length
                        $row = array_pad($row, count($header), '');
                        $rows[] = array_combine($header, $row);
                    }
                }
            } else {
                // csv / txt
                if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                    $header = null;
                    while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                        if (!$header) { $header = array_map('trim', $data); continue; }
                        $rows[] = array_combine($header, $data);
                    }
                    fclose($handle);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to parse file: ' . $e->getMessage()], 500);
        }

        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'No rows found in file. Ensure file has header row and data.'], 400);
        }

        // Validate and build attendance records
        $attendanceRecords = [];
        $date = Carbon::parse($request->date);
        $semesterModel = \App\Models\Semester::find($request->semester);
        $semesterName = $semesterModel ? $semesterModel->name : $request->semester;

        foreach ($rows as $idx => $row) {
            $regNo = $row['registration_number'] ?? $row['registration_no'] ?? null;
            $studentName = $row['name_with_initials'] ?? $row['name'] ?? null;
            $statusRaw = $row['attendance'] ?? null;
            if (!$regNo || !$studentName || !$statusRaw) continue;

            $status = strtolower(trim($statusRaw)) === 'present' ? 1 : 0;

            // Try to resolve student_id. Some DBs don't have a `registration_id` column
            // so check the schema first and fall back to student_id
            try {
                // Prefer searching by registration_id when the column is present, but run the queries
                // in separate steps to avoid building a single query that references a missing column
                // which can cause SQL errors on some environments.
                $student = null;
                if (Schema::hasColumn('students', 'registration_id')) {
                    try {
                        $student = \App\Models\Student::where('registration_id', $regNo)->first();
                    } catch (\Exception $inner) {
                        // ignore and fall back to student_id
                        $student = null;
                    }
                }

                if (!$student) {
                    // Try matching by primary key student_id
                    $student = \App\Models\Student::where('student_id', $regNo)->first();
                }
            } catch (\Exception $e) {
                // In case of any DB/schema issues, fallback to searching by primary key
                $student = \App\Models\Student::where('student_id', $regNo)->first();
            }
            if (!$student) {
                // try by name
                $student = \App\Models\Student::where('name_with_initials', 'like', '%' . $studentName . '%')->first();
            }
            if (!$student) continue;

            $attendanceRecords[] = [
                'location' => $request->location,
                'course_id' => $request->course_id,
                'intake_id' => $request->intake_id,
                'semester' => $semesterName,
                'module_id' => $request->module_id,
                'student_id' => $student->student_id,
                'status' => (bool)$status,
                'date' => $date->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($attendanceRecords)) {
            return response()->json(['success' => false, 'message' => 'No valid student rows found or students could not be matched.'], 400);
        }

        try {
            DB::beginTransaction();

            // Delete existing for that date/module
            Attendance::where('date', $date->toDateString())
                ->where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->where('semester', $semesterName)
                ->where('module_id', $request->module_id)
                ->delete();

            Attendance::insert($attendanceRecords);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Imported attendance for ' . count($attendanceRecords) . ' students.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import attendance error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to import attendance: ' . $e->getMessage()], 500);
        }
    }
} 