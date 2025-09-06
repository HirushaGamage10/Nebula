<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Semester;
use App\Models\Module;
use App\Exports\TimetableExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Timetable;
use Illuminate\Support\Facades\Validator;

class TimetableController extends Controller
{
    // Method to show the timetable view
    public function showTimetable()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        return view('timetable', compact('courses', 'intakes'));
    }

    public function store(Request $request)
    {
        try {
            // Parse timetable_data if it's a JSON string
            $timetableData = $request->input('timetable_data');
            if (is_string($timetableData)) {
                $timetableData = json_decode($timetableData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid timetable data format.'
                    ], 422);
                }
                $request->merge(['timetable_data' => $timetableData]);
            }

            $validatedData = $request->validate([
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'semester' => 'required|string',
                'specialization' => 'nullable|string',
                'timetable_data' => 'required|array',
                'timetable_data.*.time' => 'required|string',
                'timetable_data.*.monday' => 'nullable|string',
                'timetable_data.*.tuesday' => 'nullable|string',
                'timetable_data.*.wednesday' => 'nullable|string',
                'timetable_data.*.thursday' => 'nullable|string',
                'timetable_data.*.friday' => 'nullable|string',
                'timetable_data.*.saturday' => 'nullable|string',
                'timetable_data.*.sunday' => 'nullable|string',
            ]);

            // Delete existing timetable entries for this combination
            $deleteConditions = [
                'location' => $validatedData['location'],
                'course_id' => $validatedData['course_id'],
                'intake_id' => $validatedData['intake_id'],
                'semester' => $validatedData['semester']
            ];

            // Add specialization to delete conditions if provided
            if (!empty($validatedData['specialization'])) {
                $deleteConditions['specialization'] = $validatedData['specialization'];
            }

            \DB::table('timetable')->where($deleteConditions)->delete();

            // Insert new timetable entries
            $timetableEntries = [];
            $weekStartDate = $request->input('week_start_date');

            foreach ($validatedData['timetable_data'] as $row) {
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $startDate = $weekStartDate ? Carbon::parse($weekStartDate) : Carbon::now()->startOfWeek();

                foreach ($days as $index => $day) {
                    if (!empty($row[$day]) && trim($row[$day]) !== '') {
                        $date = $startDate->copy()->addDays($index);
                        $moduleId = $this->getModuleIdByName($row[$day]);

                        // Only create entry if module was found
                        if ($moduleId !== null) {
                            // Inside your loop for each subject
                            $timetableEntry = [
                                'location' => $validatedData['location'],
                                'course_id' => $validatedData['course_id'],
                                'intake_id' => $validatedData['intake_id'],
                                'semester' => $validatedData['semester'],
                                'module_id' => $moduleId,
                                'date' => $date->format('Y-m-d'),
                                'time' => $this->formatTimeForDatabase($row['time']), // Store start time
                                'end_time' => $endTimeFormatted, // Store calculated end time
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];


                            // Add specialization if provided
                            if (!empty($validatedData['specialization'])) {
                                $timetableEntry['specialization'] = $validatedData['specialization'];
                            }

                            $timetableEntries[] = $timetableEntry;
                        } else {
                            \Log::warning('Skipping timetable entry - module not found: ' . $row[$day]);
                        }
                    }
                }
            }

            if (!empty($timetableEntries)) {
                \DB::table('timetable')->insert($timetableEntries);
            }

            $message = 'Timetable saved successfully!';
            if (count($timetableEntries) === 0) {
                $message = 'No valid timetable entries found. Please check that modules are properly selected.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'entries_saved' => count($timetableEntries)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Timetable validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving timetable: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the timetable.'
            ], 500);
        }
    }

    // Helper method to get module ID by name
    private function getModuleIdByName($moduleName)
    {
        if (empty($moduleName)) {
            return null;
        }

        // Try to find module by exact name first
        $module = \App\Models\Module::where('module_name', $moduleName)->first();
        if ($module) {
            return $module->module_id;
        }

        // If not found by exact name, try to extract module code from the name (e.g., "Programming (EC98735)")
        if (preg_match('/\(([^)]+)\)/', $moduleName, $matches)) {
            $moduleCode = $matches[1];
            $module = \App\Models\Module::where('module_code', $moduleCode)->first();
            if ($module) {
                return $module->module_id;
            }
        }

        // If still not found, try to match by partial name (e.g., "Programming" from "Programming (EC98735)")
        $baseName = trim(preg_replace('/\s*\([^)]*\)/', '', $moduleName));
        if (!empty($baseName)) {
            $module = \App\Models\Module::where('module_name', $baseName)->first();
            if ($module) {
                return $module->module_id;
            }
        }

        // Log the unmatched module name for debugging
        \Log::warning('Module not found by name: ' . $moduleName);
        return null;
    }

    // Helper method to get module name by ID
    private function getModuleNameById($moduleId)
    {
        if (empty($moduleId)) {
            return '';
        }

        // If it's already a module name (contains parentheses), return as is
        if (strpos($moduleId, '(') !== false) {
            return $moduleId;
        }

        // If it's numeric, treat as module ID
        if (is_numeric($moduleId)) {
            $module = \App\Models\Module::find($moduleId);
            if ($module) {
                return $module->module_name . ' (' . $module->module_code . ')';
            }
        }

        // If it's a module name without code, try to find the module
        $module = \App\Models\Module::where('module_name', $moduleId)->first();
        if ($module) {
            return $module->module_name . ' (' . $module->module_code . ')';
        }

        return $moduleId; // Return as is if no match found
    }

    // Helper method to format time for database storage
    private function formatTimeForDatabase($timeString)
    {
        if (empty($timeString)) {
            return '00:00:00';
        }

        \Log::info('Formatting time for database:', ['input' => $timeString]);

        // Handle formats like "8-9", "8.00-9.00", "08:00-09:00"
        if (preg_match('/(\d+)[\.:-](\d+)[\.:-](\d+)[\.:-](\d+)/', $timeString, $matches)) {
            // Format: "8.00-9.00" or "08:00-09:00"
            $startHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $startMinute = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            return $startHour . ':' . $startMinute . ':00';
        } elseif (preg_match('/(\d+)[\.:-](\d+)/', $timeString, $matches)) {
            // Format: "8-9" or "8.00-9.00"
            $startHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $startMinute = isset($matches[2]) ? str_pad($matches[2], 2, '0', STR_PAD_LEFT) : '00';
            return $startHour . ':' . $startMinute . ':00';
        } elseif (preg_match('/(\d+):(\d+)/', $timeString, $matches)) {
            // Format: "8:00" or "08:00"
            $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minute = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            return $hour . ':' . $minute . ':00';
        }

        // If no pattern matches, try to parse as a simple number
        if (is_numeric($timeString)) {
            $hour = str_pad($timeString, 2, '0', STR_PAD_LEFT);
            return $hour . ':00:00';
        }

        // Default fallback
        $formattedTime = '00:00:00';
        \Log::warning('Could not parse time format, using default:', ['input' => $timeString, 'output' => $formattedTime]);
        return $formattedTime;
    }


    // Method to get existing timetable data
    public function getExistingTimetable(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'semester' => 'required|string',
                'specialization' => 'nullable|string',
            ]);

            $whereConditions = [
                'timetable.location' => $validatedData['location'],
                'timetable.course_id' => $validatedData['course_id'],
                'timetable.intake_id' => $validatedData['intake_id'],
                'timetable.semester' => $validatedData['semester']
            ];

            // Add specialization filter if provided
            if (!empty($validatedData['specialization'])) {
                $whereConditions['timetable.specialization'] = $validatedData['specialization'];
            }

            $timetableData = \DB::table('timetable')
                ->join('modules', 'timetable.module_id', '=', 'modules.module_id')
                ->where($whereConditions)
                ->select('timetable.time', 'timetable.date', 'modules.module_name', 'modules.module_code')
                ->orderBy('timetable.date')
                ->orderBy('timetable.time')
                ->get();

            // Group by time slots
            $groupedData = [];
            foreach ($timetableData as $entry) {
                $time = $entry->time;
                $dayOfWeek = strtolower(Carbon::parse($entry->date)->format('l'));
                $moduleName = $entry->module_name . ' (' . $entry->module_code . ')';

                if (!isset($groupedData[$time])) {
                    $groupedData[$time] = [
                        'time' => $time,
                        'monday' => '',
                        'tuesday' => '',
                        'wednesday' => '',
                        'thursday' => '',
                        'friday' => '',
                        'saturday' => '',
                        'sunday' => ''
                    ];
                }

                $groupedData[$time][$dayOfWeek] = $moduleName;
            }

            return response()->json([
                'success' => true,
                'timetable_data' => array_values($groupedData)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving timetable data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving timetable data.'
            ], 500);
        }
    }

    public function getIntakesForCourseAndLocation($courseId, $location)
    {
        $course = \App\Models\Course::find($courseId);
        if (!$course) {
            return response()->json(['intakes' => []]);
        }
        $intakes = \App\Models\Intake::where('course_name', $course->course_name)
            ->where('location', $location)
            ->orderBy('batch')
            ->get(['intake_id', 'batch']);
        return response()->json(['intakes' => $intakes]);
    }

    // New method to get courses by location and course type
    public function getCoursesByLocation(Request $request)
    {
        $location = $request->input('location');
        if (!$location) {
            return response()->json(['success' => false, 'courses' => []]);
        }

        try {
            $courses = Course::where('location', $location)
                ->orderBy('course_name')
                ->get(['course_id', 'course_name']);

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'courses' => [],
                'message' => 'Error fetching courses'
            ]);
        }
    }


    // Method to get active and upcoming semesters for a course and intake
    public function getSemesterDates($semesterId)
    {
        try {
            $semester = Semester::find($semesterId);
            if (!$semester) {
                return response()->json(['success' => false, 'message' => 'Semester not found']);
            }

            return response()->json([
                'success' => true,
                'start_date' => Carbon::parse($semester->start_date)->format('Y-m-d'),
                'end_date' => Carbon::parse($semester->end_date)->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching semester dates:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }
    // New method to generate weeks from start date to end date
    public function getWeeks(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            return response()->json(['weeks' => []]);
        }

        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $weeks = [];

            // Ensure we have a valid date range
            if ($start->gt($end)) {
                return response()->json(['weeks' => []]);
            }

            // Generate weeks from start date to end date
            $currentWeekStart = $start->copy()->startOfWeek();
            $weekNumber = 1;

            while ($currentWeekStart <= $end) {
                $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

                // Only include weeks that overlap with the semester period
                if ($currentWeekEnd >= $start && $currentWeekStart <= $end) {
                    $weeks[] = [
                        'week_number' => $weekNumber,
                        'start_date' => $currentWeekStart->format('Y-m-d'),
                        'end_date' => $currentWeekEnd->format('Y-m-d'),
                        'display_text' => "Week {$weekNumber} (" . $currentWeekStart->format('M d') . " - " . $currentWeekEnd->format('M d, Y') . ")"
                    ];
                    $weekNumber++;
                }

                $currentWeekStart->addWeek();
            }

            // If no weeks were generated, try a different approach
            if (empty($weeks)) {
                // Generate at least one week if the date range is valid
                $weeks[] = [
                    'week_number' => 1,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'display_text' => "Week 1 (" . $start->format('M d') . " - " . $end->format('M d, Y') . ")"
                ];
            }

            return response()->json(['weeks' => $weeks]);
        } catch (\Exception $e) {
            \Log::error('Error generating weeks:', [
                'error' => $e->getMessage(),
                'start_date' => $startDate,
                'end_date' => $request->input('end_date')
            ]);
            return response()->json(['weeks' => []]);
        }
    }

    // Method to get specializations for a course
    public function getSpecializationsForCourse(Request $request)
    {
        $courseId = $request->input('course_id');

        if (!$courseId) {
            return response()->json(['specializations' => []]);
        }

        try {
            $course = Course::find($courseId);

            if (!$course) {
                return response()->json(['specializations' => []]);
            }

            $specializations = [];
            if ($course->specializations) {
                if (is_array($course->specializations)) {
                    $specializations = $course->specializations;
                } elseif (is_string($course->specializations)) {
                    $specializations = json_decode($course->specializations, true) ?: [];
                }
            }

            // Filter out empty specializations
            $specializations = array_filter($specializations, function ($spec) {
                return !empty($spec) && trim($spec) !== '';
            });

            return response()->json(['specializations' => $specializations]);
        } catch (\Exception $e) {
            \Log::error('Error in getSpecializationsForCourse:', ['error' => $e->getMessage()]);
            return response()->json(['specializations' => []]);
        }
    }

    // Method to get modules for a specific semester with specialization filter
    public function getModulesBySemester(Request $request)
    {
        $semesterId = $request->input('semester_id');
        $specialization = $request->input('specialization');

        \Log::info('getModulesBySemester called with semester_id:', ['semester_id' => $semesterId, 'specialization' => $specialization]);

        if (!$semesterId) {
            \Log::warning('No semester_id provided');
            return response()->json(['modules' => [], 'message' => 'Semester ID is required']);
        }

        try {
            // Find the semester and eager load modules
            $semester = Semester::with('modules')->find($semesterId);

            \Log::info('Semester found:', ['semester' => $semester ? $semester->toArray() : null]);

            if (!$semester) {
                \Log::warning('Semester not found for ID:', ['semester_id' => $semesterId]);
                return response()->json(['modules' => [], 'message' => 'Semester not found']);
            }

            $modules = $semester->modules;

            // If specialization is provided, filter modules by specialization
            if ($specialization) {
                $modules = $modules->filter(function ($module) use ($specialization) {
                    // Check if the module has specialization field and matches
                    if (isset($module->specialization)) {
                        return $module->specialization === $specialization;
                    }
                    // If no specialization field, include core modules
                    return $module->module_type === 'core';
                });
            }

            // If no modules found, log the warning
            if ($modules->isEmpty()) {
                \Log::warning('No modules found for semester with specialization:', [
                    'semester_id' => $semesterId,
                    'specialization' => $specialization
                ]);
            }

            // Map the modules into a response-friendly format
            $formattedModules = $modules->map(function ($module) {
                return [
                    'module_id' => $module->module_id,
                    'module_code' => $module->module_code,
                    'module_name' => $module->module_name,
                    'full_name' => $module->module_name . ' (' . $module->module_code . ')'
                ];
            });

            \Log::info('Modules found for semester:', ['module_count' => $formattedModules->count(), 'modules' => $formattedModules->toArray()]);

            return response()->json(['modules' => $formattedModules]);
        } catch (\Exception $e) {
            \Log::error('Error in getModulesBySemester:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['modules' => [], 'message' => 'An error occurred while fetching modules']);
        }
    }


    // Method to download timetable as PDF
    public function downloadTimetablePDF(Request $request)
    {
        $courseType = $request->input('course_type');
        $location = $request->input('location');
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $semester = $request->input('semester');
        $weekNumber = $request->input('week_number');
        $timetableData = $request->input('timetable_data');
        $weekStartDate = $request->input('week_start_date');

        // Validate required parameters
        if (!$courseType || !$location || !$courseId || !$intakeId || !$startDate || !$endDate) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Parse timetable data if provided
        $parsedTimetableData = null;
        if ($timetableData) {
            try {
                $parsedTimetableData = json_decode($timetableData, true);
            } catch (\Exception $e) {
                \Log::warning('Failed to parse timetable data:', ['error' => $e->getMessage()]);
            }
        }

        try {
            // Get course details
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['error' => 'Course not found'], 404);
            }

            // Get intake details
            $intake = Intake::find($intakeId);
            if (!$intake) {
                return response()->json(['error' => 'Intake not found'], 404);
            }

            // Prepare data for PDF
            $data = [
                'courseType' => ucfirst($courseType),
                'courseName' => $course->course_name,
                'location' => $location,
                'intake' => $intake->batch,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'semester' => $semester,
                'weekNumber' => $weekNumber,
                'timetableData' => $parsedTimetableData,
                'generatedAt' => now()->format('Y-m-d H:i:s')
            ];

            // Add week start date for PDF header dates
            if ($weekStartDate) {
                $data['weekStartDate'] = $weekStartDate;
            }

            // For degree programs, get semester details
            if ($courseType === 'degree' && $semester) {
                $semesterModel = Semester::find($semester);
                if ($semesterModel) {
                    $data['semesterName'] = $semesterModel->name;
                    $data['semesterStatus'] = $semesterModel->status;

                    // Get modules for this semester
                    $modules = $semesterModel->modules;
                    $data['modules'] = $modules->map(function ($module) {
                        return [
                            'code' => $module->module_code,
                            'name' => $module->module_name,
                            'full_name' => $module->module_name . ' (' . $module->module_code . ')'
                        ];
                    });
                }
            }

            // Convert timetable data to show module names instead of IDs
            if ($parsedTimetableData) {
                $convertedTimetableData = [];
                foreach ($parsedTimetableData as $row) {
                    $convertedRow = [
                        'time' => $row['time'],
                        'monday' => $this->getModuleNameById($row['monday']),
                        'tuesday' => $this->getModuleNameById($row['tuesday']),
                        'wednesday' => $this->getModuleNameById($row['wednesday']),
                        'thursday' => $this->getModuleNameById($row['thursday']),
                        'friday' => $this->getModuleNameById($row['friday']),
                        'saturday' => $this->getModuleNameById($row['saturday']),
                        'sunday' => $this->getModuleNameById($row['sunday'])
                    ];
                    $convertedTimetableData[] = $convertedRow;
                }
                $data['timetableData'] = $convertedTimetableData;
            }

            // Generate PDF
            $pdf = PDF::loadView('pdf.timetable', $data);

            // Set PDF options
            $pdf->setPaper('A4', 'landscape');

            // Generate filename
            $filename = strtolower($courseType) . '_timetable_week_' . $weekNumber . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // Return PDF as download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Error generating timetable PDF:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }

    // Method to download timetable as Excel
    public function downloadTimetableExcel(Request $request)
    {
        $courseType = $request->input('course_type');
        $location = $request->input('location');
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $semester = $request->input('semester');
        $weekNumber = $request->input('week_number');
        $timetableData = $request->input('timetable_data');
        $weekStartDate = $request->input('week_start_date');

        // Validate required parameters
        if (!$courseType || !$location || !$courseId || !$intakeId || !$startDate || !$endDate) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Parse timetable data if provided
        $parsedTimetableData = null;
        if ($timetableData) {
            try {
                $parsedTimetableData = json_decode($timetableData, true);
            } catch (\Exception $e) {
                \Log::warning('Failed to parse timetable data:', ['error' => $e->getMessage()]);
            }
        }

        try {
            // Get course details
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['error' => 'Course not found'], 404);
            }

            // Get intake details
            $intake = Intake::find($intakeId);
            if (!$intake) {
                return response()->json(['error' => 'Intake not found'], 404);
            }

            // Convert timetable data to show module names instead of IDs
            $excelData = [];
            if ($parsedTimetableData) {
                foreach ($parsedTimetableData as $row) {
                    $excelRow = [
                        $row['time'],
                        $this->getModuleNameById($row['monday']),
                        $this->getModuleNameById($row['tuesday']),
                        $this->getModuleNameById($row['wednesday']),
                        $this->getModuleNameById($row['thursday']),
                        $this->getModuleNameById($row['friday']),
                        $this->getModuleNameById($row['saturday']),
                        $this->getModuleNameById($row['sunday'])
                    ];
                    $excelData[] = $excelRow;
                }
            }

            // Generate filename
            $filename = strtolower($courseType) . '_timetable_week_' . $weekNumber . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Return Excel file as download
            return Excel::download(
                new TimetableExport($excelData, $courseType, $course->course_name, $location, $intake->batch),
                $filename
            );
        } catch (\Exception $e) {
            \Log::error('Error generating timetable Excel:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Failed to generate Excel'], 500);
        }
    }

    public function getTimetableEvents(Request $request)
    {
        \Log::info('getTimetableEvents called', $request->all());

        // Validate incoming filters
        $validatedData = $request->validate([
            'location' => 'required|string',
            'course_id' => 'required|integer',
            'intake_id' => 'required|integer',
            'semester' => 'required', // accept id or name
        ]);

        \Log::info('getTimetableEvents validated', $validatedData);

        // If semester is an ID, convert to semester name/value stored in timetable
        if (is_numeric($validatedData['semester'])) {
            $sem = Semester::find((int)$validatedData['semester']);
            if ($sem) {
                $validatedData['semester'] = (string) $sem->name;
            }
        }

        // Retrieve timetable data based on filters
        $events = \DB::table('timetable')
            ->join('modules', 'timetable.module_id', '=', 'modules.module_id')
            ->where([
                ['timetable.location', '=', $validatedData['location']],
                ['timetable.course_id', '=', $validatedData['course_id']],
                ['timetable.intake_id', '=', $validatedData['intake_id']],
                ['timetable.semester', '=', $validatedData['semester']],
            ])
            ->select('timetable.*', 'modules.module_name', 'modules.module_code')
            ->get();

        \Log::info('getTimetableEvents DB rows count: ' . $events->count(), ['rows' => $events->toArray()]);

    // Map data to FullCalendar format
    $calendarEvents = $events->map(function ($event) {
            // ensure proper ISO datetimes (add seconds if needed)
            $time = $event->time;
            if (strpos($time, ':') && substr_count($time, ':') === 1) {
                $time .= ':00';
            }
            $startIso = $event->date . 'T' . $time;
            $endTime = $event->end_time ?? $time;
            if (strpos($endTime, ':') && substr_count($endTime, ':') === 1) {
                $endTime .= ':00';
            }
            $endIso = $event->date . 'T' . $endTime;

            return [
                'id' => $event->id ?? null,
        'title' => $event->module_name,
        'date' => $event->date,
        'time' => $time,
        'end_time' => $endTime,
        'start' => $startIso,
        'end' => $endIso,
        'classroom' => $event->classroom ?? null,
        'lecturer' => $event->lecturer ?? null,
        'module_code' => $event->module_code ?? null,
        'module_name' => $event->module_name ?? null,
            ];
        });

        return response()->json([
            'events' => $calendarEvents,
            'raw_rows' => $events // temporary debug payload
        ]);
    }


    public function getSemesters(Request $request)
    {
        try {
            $courseId = $request->input('course_id');
            $intakeId = $request->input('intake_id');

            // Fetch the semesters based on course and intake
            $semesters = Semester::where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->get();

            return response()->json([
                'success' => true,
                'semesters' => $semesters
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function getAvailableSubjects(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        try {
            $date = Carbon::parse($validated['date']);

            // Fetch subjects available for the specific date (You may have a specific model to do this)
            // Example fetch from `modules` table, or adjust according to your DB structure
            $subjects = \App\Models\Module::where('available_on', $date->format('Y-m-d'))
                ->get(['module_name', 'module_code']);

            // Return subjects as JSON response
            return response()->json(['success' => true, 'subjects' => $subjects]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching subjects']);
        }
    }
    public function assignSubjectToTimeslot(Request $request)
    {
        \Log::info('assignSubjectToTimeslot payload', $request->all()); // add this line

        // Custom validator to accept HH:mm optionally with AM/PM
        $rules = [
            'date' => 'required|date',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:modules,module_id',
            'durations' => 'required|array|min:1',
            'durations.*' => 'numeric|min:1',
            'times' => 'required|array|min:1',
            'times.*' => ['required','regex:/^(?:([01]\d|2[0-3]):([0-5]\d))(?:\s?(AM|PM))?$/i'],
            'end_times' => 'required|array|min:1',
            'end_times.*' => ['required','regex:/^(?:([01]\d|2[0-3]):([0-5]\d))(?:\s?(AM|PM))?$/i'],
            'location' => 'required|string',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required|string',
            'classrooms' => 'nullable|array',
            'classrooms.*' => 'nullable|string',
            'lecturers' => 'nullable|array',
            'lecturers.*' => 'nullable|string',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::warning('assignSubjectToTimeslot validation failed', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // normalize semester: incoming is semester id, DB expects semester name/number
        $semesterValue = $validated['semester'];
        if (is_numeric($semesterValue)) {
            $semesterModel = Semester::find($semesterValue);
            if ($semesterModel) {
                $semesterValue = (string) $semesterModel->name; // name is e.g. '1','2' etc.
            }
        }

        try {
            \DB::beginTransaction();

            foreach ($validated['subject_ids'] as $index => $subjectId) {
                $subject = \App\Models\Module::find($subjectId);
                if (!$subject) {
                    \DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Subject not found.'], 404);
                }

                // Normalize start time to 24-hour "H:i"
                $rawStart = $validated['times'][$index];
                $rawEnd = $validated['end_times'][$index];

                try {
                    // try 24-hour
                    $start = Carbon::createFromFormat('H:i', $rawStart);
                } catch (\Exception $e) {
                    try {
                        // try 12-hour with AM/PM
                        $start = Carbon::createFromFormat('h:i A', $rawStart);
                    } catch (\Exception $e2) {
                        $start = Carbon::parse($rawStart);
                    }
                }

                try {
                    $end = Carbon::createFromFormat('H:i', $rawEnd);
                } catch (\Exception $e) {
                    try {
                        $end = Carbon::createFromFormat('h:i A', $rawEnd);
                    } catch (\Exception $e2) {
                        $end = Carbon::parse($rawEnd);
                    }
                }

                $assignment = new \App\Models\Timetable();
                $assignment->date = $validated['date'];
                $assignment->module_id = $subjectId;
                $assignment->subject_id = $subjectId;            // ensure DB-required column is set
                $assignment->location = $validated['location'];
                $assignment->course_id = $validated['course_id'];
                $assignment->intake_id = $validated['intake_id'];
                $assignment->semester = $semesterValue;         // use normalized semester value
                $assignment->duration = $validated['durations'][$index];
                $assignment->time = $start->format('H:i');
                $assignment->end_time = $end->format('H:i');
                // optional classroom and lecturer
                $assignment->classroom = $validated['classrooms'][$index] ?? null;
                $assignment->lecturer = $validated['lecturers'][$index] ?? null;
                $assignment->save();
            }
            \DB::commit();
            return response()->json(['success' => true, 'message' => 'Subjects assigned successfully']);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error assigning subjects: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            // return the error message and trace for debugging (remove trace later)
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


    // Accepts arrays: subject_ids, durations, times, end_times and a semester (id or label).
    public function assignSubjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'required',
            'durations' => 'required|array',
            'durations.*' => 'required|integer|min:1',
            'times' => 'required|array',
            'times.*' => 'required|string',
            'end_times' => 'required|array',
            'end_times.*' => 'required|string',
            'classrooms' => 'nullable|array',
            'classrooms.*' => 'nullable|string',
            'lecturers' => 'nullable|array',
            'lecturers.*' => 'nullable|string',
            'location' => 'nullable|string',
            'course_id' => 'nullable|integer',
            'intake_id' => 'nullable|integer',
            'semester' => 'nullable' // can be semester id or label
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Normalize semester: if numeric => lookup Semester.name, otherwise use as-is (string)
        $semesterInput = $request->input('semester');
        $semesterValue = null;
        if (!is_null($semesterInput) && $semesterInput !== '') {
            if (is_numeric($semesterInput)) {
                $sem = Semester::find((int) $semesterInput);
                $semesterValue = $sem ? (string)$sem->name : (string)$semesterInput;
            } else {
                $semesterValue = (string)$semesterInput;
            }
            // ensure safe length (column in DB may be tiny)
            $semesterValue = substr($semesterValue, 0, 10);
        }

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $subjectIds = $request->input('subject_ids', []);
        $durations = $request->input('durations', []);
        $times = $request->input('times', []);
        $endTimes = $request->input('end_times', []);

        $created = [];
        \DB::beginTransaction();
        try {
            foreach ($subjectIds as $i => $subId) {
                $startRaw = $times[$i] ?? null;
                $endRaw = $endTimes[$i] ?? null;
                $duration = intval($durations[$i] ?? 0);

                $start = $startRaw ? Carbon::parse($startRaw)->format('H:i:s') : null;
                $end = $endRaw ? Carbon::parse($endRaw)->format('H:i:s') : null;

                $row = Timetable::create([
                    'location'   => $request->input('location'),
                    'course_id'  => $request->input('course_id'),
                    'intake_id'  => $request->input('intake_id'),
                    'semester'   => $semesterValue,
                    'date'       => $date,
                    'time'       => $start,
                    'end_time'   => $end,
                    'duration'   => $duration,
                    'module_id'  => $subId,
                    'subject_id' => $subId, // changed: save subject_id instead of null
                    'classroom'  => $request->input('classrooms')[$i] ?? null,
                    'lecturer'   => $request->input('lecturers')[$i] ?? null,
                ]);

                $created[] = $row;
            }

            \DB::commit();
            return response()->json(['success' => true, 'created' => $created], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('assignSubjects error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // Delete a single timetable event by id
    public function deleteEvent(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'No id provided'], 422);
        }

        try {
            $row = Timetable::find($id);
            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Event not found'], 404);
            }
            $row->delete();
            return response()->json(['success' => true, 'message' => 'Event deleted']);
        } catch (\Exception $e) {
            \Log::error('Error deleting timetable event: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
