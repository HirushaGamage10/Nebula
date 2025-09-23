<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;
use App\Models\Student;
use App\Models\ExamResult;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamResultController extends Controller
{
    /**
     * Show the student exam result management view.
     */
    public function showStudentExamResultManagement()
    {
        $courses = Course::where('course_type', 'degree')->orderBy('course_name')->get();
        $modules = Module::orderBy('module_name')->get();
        $intakes = Intake::join('courses', 'intakes.course_name', '=', 'courses.course_name')
            ->select('intakes.*', 'courses.course_name as course_display_name')
            ->get()
            ->map(function ($intake) {
                $intake->intake_display_name = $intake->course_display_name . ' - ' . $intake->intake_no;
                return $intake;
            });

        return view('exam_results', compact('courses', 'modules', 'intakes'));
    }

    /**
     * Get course data including modules, semesters, and years.
     */
    public function getCourseData($courseID)
    {
        try {
            $course = Course::with(['modules'])->find($courseID);

            if ($course) {
                // Assuming 'duration' is in years and 'no_of_semesters' is the total.
                // The range of years will be from 1 up to the course duration.
                $years = range(1, (int)$course->duration); 
                
                // Get actual created semesters for this course
                $semesters = \App\Models\Semester::where('course_id', $courseID)
                    ->whereIn('status', ['active', 'upcoming'])
                    ->select('id', 'name')
                    ->get();

                return response()->json([
                    'modules' => $course->modules,
                    'semesters' => $semesters,
                ]);
            }

            return response()->json(['error' => 'Course not found or invalid data.'], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            \Log::error('Error in getCourseData for course ID ' . $courseID . ': ' . $e->getMessage());
            return response()->json(['error' => 'An internal server error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Get student name by ID.
     */
    public function getStudentName(Request $request)
    {
        try {
            $student = Student::where('student_id', $request->input('student_id'))->first();

            if ($student) {
                return response()->json(['success' => true, 'name' => $student->full_name]);
            }
            return response()->json(['success' => false, 'message' => 'Student not found.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a new exam result.
     */
    public function storeResult(Request $request)
    {
        try {
            \Log::info('storeResult called with data:', $request->all());
            
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'semester' => 'required',
                'module_id' => 'required|exists:modules,module_id',
                'results' => 'required|array|min:1',
                'results.*.student_id' => 'required|exists:students,student_id',
                'results.*.marks' => 'nullable|integer|min:0|max:100',
                'results.*.grade' => 'nullable|string|max:5',
                'results.*.remarks' => 'nullable|string|max:255',
            ]);
            
            \Log::info('Validation passed, validated data:', $validatedData);

            // Get the semester to convert ID to name
            $semester = \App\Models\Semester::find($validatedData['semester']);
            if (!$semester) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semester not found.'
                ], 404);
            }

            DB::beginTransaction();
            \Log::info('Database transaction started');

            $createdCount = 0;
            $updatedCount = 0;
            
            foreach ($validatedData['results'] as $index => $result) {
                \Log::info("Processing result {$index}:", $result);
                
                // Check if result already exists
                $existingResult = ExamResult::where('student_id', $result['student_id'])
                    ->where('course_id', $validatedData['course_id'])
                    ->where('module_id', $validatedData['module_id'])
                    ->where('intake_id', $validatedData['intake_id'])
                    ->where('location', $validatedData['location'])
                    ->where('semester', $semester->name)
                    ->first();

                if ($existingResult) {
                    // Update existing result
                    \Log::info("Updating existing result for student {$result['student_id']}");
                    $existingResult->update([
                        'marks' => $result['marks'] ?? null,
                        'grade' => $result['grade'] ?? null,
                        'remarks' => $result['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                } else {
                    // Create new result
                    \Log::info("Creating new result for student {$result['student_id']}");
                    ExamResult::create([
                        'student_id' => $result['student_id'],
                        'course_id' => $validatedData['course_id'],
                        'module_id' => $validatedData['module_id'],
                        'intake_id' => $validatedData['intake_id'],
                        'location' => $validatedData['location'],
                        'semester' => $semester->name,
                        'marks' => $result['marks'] ?? null,
                        'grade' => $result['grade'] ?? null,
                        'remarks' => $result['remarks'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();
            \Log::info("Database transaction committed. Created: {$createdCount}, Updated: {$updatedCount}");

            $totalCount = $createdCount + $updatedCount;
            return response()->json([
                'success' => true, 
                'message' => "Exam results stored successfully for {$totalCount} student(s)."
            ], Response::HTTP_CREATED);

        } catch (QueryException $e) {
            DB::rollBack();
            \Log::error('Database error storing exam result: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed.', 
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing exam result: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while storing the results.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get intakes for a given course and location.
     */
    public function getIntakesForCourseAndLocation($courseID, $location)
    {
        try {
            $course = \App\Models\Course::find($courseID);
            if (!$course) {
                return response()->json(['error' => 'Course not found.'], 404);
            }
            $intakes = \App\Models\Intake::where('course_name', $course->course_name)
                ->where('location', $location)
                ->orderBy('batch')
                ->get(['intake_id', 'batch']);

            return response()->json(['intakes' => $intakes]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get modules filtered by course, intake, year, semester, and location.
     */
    public function getFilteredModules(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'semester' => 'required|string',
            'location' => 'required|string',
        ]);

        $courseId = $request->input('course_id');
        $semesterId = $request->input('semester');

        // Get the semester by ID
        $semester = \App\Models\Semester::where('course_id', $courseId)
            ->where('intake_id', $request->input('intake_id'))
            ->where('id', $semesterId)
            ->first();

        \Log::info('getFilteredModules called with:', [
            'course_id' => $courseId,
            'intake_id' => $request->input('intake_id'),
            'semester_id' => $semesterId,
            'semester_found' => $semester ? 'yes' : 'no'
        ]);

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

    public function getCoursesByLocation(Request $request)
    {
        $location = $request->query('location');
        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location is required.']);
        }
        try {
            $courses = Course::select('course_id', 'course_name')
                ->where('location', $location)
                ->orderBy('course_name', 'asc')
                ->get();

            if ($courses->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No courses found for this location.']);
            }

            return response()->json(['success' => true, 'courses' => $courses]);
        } catch (\Exception $e) {
            Log::error('Error fetching courses by location: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching courses.'], 500);
        }
    }

    public function getSemesters(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
        ]);

        $course = \App\Models\Course::find($request->course_id);
        $intake = \App\Models\Intake::find($request->intake_id);

        if (!$course || !$intake) {
            return response()->json(['error' => 'Invalid course or intake.'], 404);
        }

        // Get semesters that have been created for this course and intake
        $semesters = \App\Models\Semester::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->whereIn('status', ['active', 'upcoming'])
            ->select('id', 'name')
            ->get();

        \Log::info('ExamResultController getSemesters called with:', [
            'course_id' => $request->course_id,
            'intake_id' => $request->intake_id,
            'semesters_found' => $semesters->count(),
            'semesters_data' => $semesters->toArray()
        ]);
        
        return response()->json(['semesters' => $semesters]);
    }

    public function getStudentsForExamResult(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'location' => 'required|string',
            'semester' => 'required',
            'module_id' => 'required|integer|exists:modules,module_id',
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
        $isCoreModule = \DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->where('module_id', $moduleId)
            ->exists();

        // Check if exam results already exist for this module
        $existingResults = ExamResult::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->where('location', $location)
            ->where('semester', $semester->name)
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
                ->map(function($reg) use ($request, $existingResults, $semester) {
                    $studentData = [
                        'registration_id' => $reg->student->registration_id ?? $reg->student->student_id,
                        'student_id' => $reg->student->student_id,
                        'name' => $reg->student->full_name,
                    ];

                    // If results exist, fetch the existing marks and grade
                    if ($existingResults) {
                        $existingResult = ExamResult::where('course_id', $request->course_id)
                            ->where('intake_id', $request->intake_id)
                            ->where('location', $request->location)
                            ->where('semester', $semester->name)
                            ->where('module_id', $request->module_id)
                            ->where('student_id', $reg->student->student_id)
                            ->first();

                        if ($existingResult) {
                            $studentData['marks'] = $existingResult->marks;
                            $studentData['grade'] = $existingResult->grade;
                        } else {
                            $studentData['marks'] = '';
                            $studentData['grade'] = '';
                        }
                    } else {
                        $studentData['marks'] = '';
                        $studentData['grade'] = '';
                    }

                    return $studentData;
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
                ->map(function($reg) use ($request, $existingResults, $semester) {
                    $studentData = [
                        'registration_id' => $reg->student->registration_id ?? $reg->student->student_id,
                        'student_id' => $reg->student->student_id,
                        'name' => $reg->student->full_name,
                    ];

                    // If results exist, fetch the existing marks and grade
                    if ($existingResults) {
                        $existingResult = ExamResult::where('course_id', $request->course_id)
                            ->where('intake_id', $request->intake_id)
                            ->where('location', $request->location)
                            ->where('semester', $semester->name)
                            ->where('module_id', $request->module_id)
                            ->where('student_id', $reg->student->student_id)
                            ->first();

                        if ($existingResult) {
                            $studentData['marks'] = $existingResult->marks;
                            $studentData['grade'] = $existingResult->grade;
                        } else {
                            $studentData['marks'] = '';
                            $studentData['grade'] = '';
                        }
                    } else {
                        $studentData['marks'] = '';
                        $studentData['grade'] = '';
                    }

                    return $studentData;
                });
        }

        return response()->json([
            'success' => true,
            'students' => $students,
            'results_exist' => $existingResults
        ]);
    }

    /**
     * Show the exam results view and edit page.
     */
    public function showExamResultsViewEdit()
    {
        return view('exam_results_view_edit');
    }

    /**
     * Get existing exam results for viewing and editing.
     */
    public function getExistingExamResults(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,course_id',
            'intake_id' => 'required|integer|exists:intakes,intake_id',
            'location' => 'required|string',
            'semester' => 'required',
            'module_id' => 'required|integer|exists:modules,module_id',
        ]);

        // Get the semester to convert ID to name
        $semester = \App\Models\Semester::find($request->semester);
        if (!$semester) {
            return response()->json([
                'success' => false,
                'message' => 'Semester not found.'
            ], 404);
        }

        $results = ExamResult::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where('semester', $semester->name)
            ->where('module_id', $request->module_id)
            ->with(['student.courseRegistrations', 'course', 'module', 'intake'])
            ->get()
            ->map(function($result) {
                $registration = $result->student->courseRegistrations
                    ->where('course_id', $result->course_id)
                    ->where('intake_id', $result->intake_id)
                    ->first();
                
                return [
                    'id' => $result->id,
                    'student_id' => $result->student_id,
                    'registration_id' => $registration ? $registration->course_registration_id : '',
                    'student_name' => $result->student->full_name,
                    'marks' => $result->marks,
                    'grade' => $result->grade,
                    'created_at' => $result->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $result->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'results' => $results,
            'total_count' => $results->count()
        ]);
    }

    /**
     * Update existing exam results.
     */
    public function updateResult(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'semester' => 'required',
                'module_id' => 'required|exists:modules,module_id',
                'results' => 'required|array|min:1',
                'results.*.id' => 'required|exists:exam_results,id',
                'results.*.marks' => 'nullable|integer|min:0|max:100',
                'results.*.grade' => 'nullable|string|max:5',
                'results.*.remarks' => 'nullable|string|max:255',
            ]);

            // Get the semester to convert ID to name
            $semester = \App\Models\Semester::find($validatedData['semester']);
            if (!$semester) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semester not found.'
                ], 404);
            }

            DB::beginTransaction();

            $updatedCount = 0;
            foreach ($validatedData['results'] as $result) {
                $examResult = ExamResult::find($result['id']);
                if ($examResult) {
                    $examResult->update([
                        'marks' => $result['marks'] ?? null,
                        'grade' => $result['grade'] ?? null,
                        'remarks' => $result['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => "Successfully updated {$updatedCount} exam result(s)."
            ], Response::HTTP_OK);

        } catch (QueryException $e) {
            DB::rollBack();
            \Log::error('Database error updating exam result: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed.', 
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating exam result: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while updating the results.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Auto-calculate grades from marks for existing exam results.
     */
    public function autoCalculateGrades(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
                'semester' => 'required',
                'module_id' => 'required|exists:modules,module_id',
            ]);

            $updatedCount = ExamResult::autoCalculateGrades(
                $validatedData['course_id'],
                $validatedData['module_id'],
                $validatedData['intake_id']
            );

            return response()->json([
                'success' => true,
                'message' => "Successfully auto-calculated grades for {$updatedCount} exam result(s).",
                'updated_count' => $updatedCount
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error auto-calculating grades: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while auto-calculating grades.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Download template CSV with actual student data
     */
    public function downloadTemplate(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|integer|exists:courses,course_id',
                'intake_id' => 'required|integer|exists:intakes,intake_id',
                'location' => 'required|string',
                'semester' => 'required',
                'module_id' => 'required|integer|exists:modules,module_id',
            ]);

            $courseId = $request->course_id;
            $intakeId = $request->intake_id;
            $location = $request->location;
            $semesterId = $request->semester;
            $moduleId = $request->module_id;

            // Get course, intake, semester, and module names
            $course = Course::find($courseId);
            $intake = Intake::find($intakeId);
            $semester = \App\Models\Semester::find($semesterId);
            $module = Module::find($moduleId);

            if (!$course || !$intake || !$semester || !$module) {
                return response()->json(['error' => 'Invalid course, intake, semester, or module.'], 404);
            }

            Log::info('Download template called with:', [
                'course_id' => $courseId,
                'intake_id' => $intakeId,
                'location' => $location,
                'semester_id' => $semesterId,
                'module_id' => $moduleId,
                'course_name' => $course->course_name,
                'intake_no' => $intake->intake_no,
                'semester_name' => $semester->name,
                'module_name' => $module->module_name
            ]);

            $students = collect();

            // First, try to get students from semester registrations
            $semesterRegistrations = \App\Models\SemesterRegistration::where('semester_id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->where('location', $location)
                ->where('status', 'registered')
                ->with('student')
                ->get();

            Log::info('SemesterRegistration query result:', [
                'count' => $semesterRegistrations->count(),
                'student_ids' => $semesterRegistrations->pluck('student_id')->toArray()
            ]);

            if ($semesterRegistrations->count() > 0) {
                $students = $semesterRegistrations->map(function($reg) use ($course, $module, $intake, $semester, $location) {
                    return [
                        'Student Name' => $reg->student->full_name,
                        'Course Name' => $course->course_name,
                        'Module Name' => $module->module_name,
                        'Intake' => $intake->intake_no ?? $intake->batch ?? '2025-August',
                        'Location' => $location,
                        'Semester' => $semester->name,
                        'Marks' => '',
                        'Grade' => '',
                        'Remarks' => ''
                    ];
                });
            } else {
                // Fallback: Try module management for elective modules
                $moduleRegistrations = \App\Models\ModuleManagement::where('module_id', $moduleId)
                    ->where('course_id', $courseId)
                    ->where('intake_id', $intakeId)
                    ->where('location', $location)
                    ->with('student')
                    ->get();

                Log::info('ModuleManagement query result:', [
                    'count' => $moduleRegistrations->count(),
                    'student_ids' => $moduleRegistrations->pluck('student_id')->toArray()
                ]);

                if ($moduleRegistrations->count() > 0) {
                    $students = $moduleRegistrations->map(function($reg) use ($course, $module, $intake, $semester, $location) {
                        return [
                            'Student Name' => $reg->student->full_name,
                            'Course Name' => $course->course_name,
                            'Module Name' => $module->module_name,
                            'Intake' => $intake->intake_no ?? $intake->batch ?? '2025-August',
                            'Location' => $location,
                            'Semester' => $semester->name,
                            'Marks' => '',
                            'Grade' => '',
                            'Remarks' => ''
                        ];
                    });
                } else {
                    // Final fallback: Get all students registered for this course and intake
                    $courseRegistrations = \App\Models\CourseRegistration::where('course_id', $courseId)
                        ->where('intake_id', $intakeId)
                        ->where('location', $location)
                        ->whereIn('approval_status', ['approved', 'registered'])
                        ->with('student')
                        ->get();

                    Log::info('CourseRegistration fallback query result:', [
                        'count' => $courseRegistrations->count(),
                        'student_ids' => $courseRegistrations->pluck('student_id')->toArray()
                    ]);

                    $students = $courseRegistrations->map(function($reg) use ($course, $module, $intake, $semester, $location) {
                        return [
                            'Student Name' => $reg->student->full_name,
                            'Course Name' => $course->course_name,
                            'Module Name' => $module->module_name,
                            'Intake' => $intake->intake_no ?? $intake->batch ?? '2025-August',
                            'Location' => $location,
                            'Semester' => $semester->name,
                            'Marks' => '',
                            'Grade' => '',
                            'Remarks' => ''
                        ];
                    });
                }
            }

            Log::info('Final student count for template:', ['count' => $students->count()]);

            // Create CSV content
            $csvContent = [];
            
            // Add headers
            if ($students->count() > 0) {
                $csvContent[] = implode(',', array_keys($students->first()));
                
                // Add student data rows
                foreach ($students as $student) {
                    $row = [];
                    foreach ($student as $value) {
                        // Properly escape values that contain commas, quotes, or newlines
                        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
                            $row[] = '"' . str_replace('"', '""', $value) . '"';
                        } else {
                            $row[] = $value;
                        }
                    }
                    $csvContent[] = implode(',', $row);
                }
            } else {
                // If no students found, create template with headers only
                $csvContent[] = 'Student Name,Course Name,Module Name,Intake,Location,Semester,Marks,Grade,Remarks';
                Log::warning('No students found for template generation');
            }

            $csv = implode("\n", $csvContent);

            // Create response with CSV content
            $filename = 'exam_results_template_' . 
                       str_replace(' ', '_', $course->course_name) . '_' . 
                       str_replace(' ', '_', $module->module_name) . '_' . 
                       $intake->intake_no . '.csv';

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while downloading template.'
            ], 500);
        }
    }
}
