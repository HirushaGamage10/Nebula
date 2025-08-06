<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Semester;
use App\Models\Module;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
                    if (!empty($row[$day])) {
                        $date = $startDate->copy()->addDays($index);
                        
                        $timetableEntry = [
                            'location' => $validatedData['location'],
                            'course_id' => $validatedData['course_id'],
                            'intake_id' => $validatedData['intake_id'],
                            'semester' => $validatedData['semester'],
                            'module_id' => $this->getModuleIdByName($row[$day]),
                            'date' => $date->format('Y-m-d'),
                            'time' => $row['time'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        // Add specialization if provided
                        if (!empty($validatedData['specialization'])) {
                            $timetableEntry['specialization'] = $validatedData['specialization'];
                        }
                        
                        $timetableEntries[] = $timetableEntry;
                    }
                }
            }

            if (!empty($timetableEntries)) {
                \DB::table('timetable')->insert($timetableEntries);
            }

            return response()->json([
                'success' => true,
                'message' => 'Timetable saved successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving timetable: ' . $e->getMessage());
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
        
        // Try to find module by name
        $module = \App\Models\Module::where('module_name', $moduleName)->first();
        if ($module) {
            return $module->module_id;
        }
        
        // If not found by name, try to extract module code from the name
        if (preg_match('/\(([^)]+)\)/', $moduleName, $matches)) {
            $moduleCode = $matches[1];
            $module = \App\Models\Module::where('module_code', $moduleCode)->first();
            if ($module) {
                return $module->module_id;
            }
        }
        
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
        $courseType = $request->input('course_type');

        if (!$location || !$courseType) {
            return response()->json(['success' => false, 'courses' => []]);
        }

        try {
            $courses = Course::where('location', $location)
                ->where('course_type', $courseType)
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
    public function getSemesters(Request $request)
    {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');

        if (!$courseId || !$intakeId) {
            return response()->json(['semesters' => []]);
        }

        try {
            // Get only active and upcoming semesters
            $semesters = Semester::where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->whereIn('status', ['active', 'upcoming'])
                ->orderBy('start_date')
                ->get(['id', 'name', 'start_date', 'end_date', 'status']);

            $formattedSemesters = $semesters->map(function($semester) {
                return [
                    'id' => $semester->id,
                    'name' => $semester->name,
                    'start_date' => $semester->start_date,
                    'end_date' => $semester->end_date,
                    'status' => $semester->status
                ];
            });
            
            return response()->json(['semesters' => $formattedSemesters]);
        } catch (\Exception $e) {
            return response()->json(['semesters' => []]);
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

            return response()->json(['weeks' => $weeks]);
        } catch (\Exception $e) {
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
            $specializations = array_filter($specializations, function($spec) {
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
            return response()->json(['modules' => []]);
        }

        try {
            $semester = Semester::with('modules')->find($semesterId);
            
            \Log::info('Semester found:', ['semester' => $semester ? $semester->toArray() : null]);
            
            if (!$semester) {
                \Log::warning('Semester not found for ID:', ['semester_id' => $semesterId]);
                return response()->json(['modules' => []]);
            }

            $modules = $semester->modules;

            // Filter modules by specialization if provided
            if ($specialization) {
                $modules = $modules->filter(function($module) use ($specialization) {
                    // Check if module has specialization field and matches
                    if (isset($module->specialization)) {
                        return $module->specialization === $specialization;
                    }
                    // If no specialization field, include core modules
                    return $module->module_type === 'core';
                });
            }

            $formattedModules = $modules->map(function($module) {
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
            return response()->json(['modules' => []]);
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
                    $data['modules'] = $modules->map(function($module) {
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
}
