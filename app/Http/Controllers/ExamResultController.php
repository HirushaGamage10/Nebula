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
                
                // The range of semesters will be from 1 up to the total number of semesters.
                $semesters = range(1, $course->no_of_semesters);

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
            ]);

            foreach ($validatedData['results'] as $result) {
                ExamResult::create([
                    'student_id' => $result['student_id'],
                    'course_id' => $validatedData['course_id'],
                    'module_id' => $validatedData['module_id'],
                    'intake_id' => $validatedData['intake_id'],
                    'location' => $validatedData['location'],
                    'semester' => $validatedData['semester'],
                    'marks' => $result['marks'] ?? null,
                    'grade' => $result['grade'] ?? null,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Exam results stored successfully.'], Response::HTTP_CREATED);

        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            \Log::error('Error storing exam result: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while storing the results.'], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        $semesterName = $request->input('semester');

        // Get the semester ID from the semester name
        $semester = \App\Models\Semester::where('course_id', $courseId)
            ->where('intake_id', $request->input('intake_id'))
            ->where('name', $semesterName)
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

        // Check if exam results already exist for this module
        $existingResults = ExamResult::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where('semester', $request->semester)
            ->where('module_id', $request->module_id)
            ->exists();

        $students = CourseRegistration::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where(function($query) {
                $query->where('status', 'Registered')
                      ->orWhere('approval_status', 'Approved by DGM');
            })
            ->with('student')
            ->get()
            ->map(function($reg) use ($request, $existingResults) {
                $studentData = [
                    'registration_id' => $reg->course_registration_id ?? $reg->id,
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->full_name,
                ];

                // If results exist, fetch the existing marks and grade
                if ($existingResults) {
                    $existingResult = ExamResult::where('course_id', $request->course_id)
                        ->where('intake_id', $request->intake_id)
                        ->where('location', $request->location)
                        ->where('semester', $request->semester)
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

        $results = ExamResult::where('course_id', $request->course_id)
            ->where('intake_id', $request->intake_id)
            ->where('location', $request->location)
            ->where('semester', $request->semester)
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
                    'remarks' => $result->remarks,
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
                'results.*.marks' => 'required|integer|min:0|max:100',
                'results.*.grade' => 'required|string|max:5',
                'results.*.remarks' => 'nullable|string|max:255',
            ]);

            $updatedCount = 0;
            foreach ($validatedData['results'] as $result) {
                $examResult = ExamResult::find($result['id']);
                if ($examResult) {
                    $examResult->update([
                        'marks' => $result['marks'],
                        'grade' => $result['grade'],
                        'remarks' => $result['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true, 
                'message' => "Successfully updated {$updatedCount} exam result(s)."
            ], Response::HTTP_OK);

        } catch (QueryException $e) {
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
            \Log::error('Error updating exam result: ' . $e->getMessage());
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
}
