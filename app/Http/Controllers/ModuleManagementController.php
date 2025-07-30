<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Student;
use App\Models\ModuleManagement;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Semester;

class ModuleManagementController extends Controller
{
    /**
     * Display the module management page.
     */
    public function showModuleManagement()
    {
        if (!Auth::check() || !Auth::user()->status) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $courses = Course::where('course_type', 'degree')->orderBy('course_name')->get();
        $modules = Module::orderBy('module_name')->get();

        return view('module_management', compact('courses', 'modules'));
    }

    /**
     * Get intakes for selected course and location
     */
    public function getIntakes(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_type' => 'nullable|string'
        ]);

        try {
            // Get the course name for the given course_id
            $course = \App\Models\Course::find($request->course_id);
            if (!$course) {
                return response()->json(['success' => false, 'data' => []]);
            }

            // Build the query
            $query = \App\Models\Intake::where('course_name', $course->course_name)
                ->where('location', $request->location);

            // Add course_type filter if provided
            if ($request->has('course_type') && $request->course_type) {
                $query->where('intake_type', $request->course_type);
            }

            // Get intakes ordered by batch
            $intakes = $query->orderBy('batch')
                ->get(['intake_id', 'batch as intake_name']);

            return response()->json([
                'success' => true,
                'data' => $intakes
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching intakes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching intakes.'
            ], 500);
        }
    }

    /**
     * Get students for selected intake and semester
     */
    public function getStudents(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required|string',
            'course_id' => 'required|exists:courses,course_id'
        ]);

        try {
            // Debug: Log the request parameters
            \Log::info('getStudents called with:', [
                'intake_id' => $request->intake_id,
                'course_id' => $request->course_id,
                'semester' => $request->semester
            ]);
            
            // Get students who have registered for this semester through semester registration
            $students = \App\Models\SemesterRegistration::where('intake_id', $request->intake_id)
                                                       ->where('course_id', $request->course_id)
                                                       ->where('semester_id', $request->semester)
                                                       ->with(['student:id,student_id,first_name,last_name,nic'])
                                                       ->get();
            
            // Debug: Log the query results
            \Log::info('SemesterRegistration query results for getStudents:', [
                'count' => $students->count(),
                'registrations' => $students->toArray()
            ]);
            
            $mappedStudents = $students->map(function ($registration) {
                return [
                    'student_id' => $registration->student->student_id,
                    'name' => $registration->student->first_name . ' ' . $registration->student->last_name,
                    'nic' => $registration->student->nic
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $mappedStudents
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching students.'
            ], 500);
        }
    }

    /**
     * Get modules for selected course
     */
    public function getModules(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,course_id'
        ]);

        try {
            // Return all modules since modules table doesn't have course_id column
            $modules = Module::orderBy('module_name')->get();

            return response()->json([
                'success' => true,
                'data' => $modules
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching modules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching modules.'
            ], 500);
        }
    }

    /**
     * Get current module assignments for students
     */
    public function getModuleAssignments(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required|in:1,2,3,4,5,6'
        ]);

        try {
            $assignments = ModuleManagement::where('intake_id', $request->intake_id)
                                         ->where('semester', $request->semester)
                                         ->with(['student:id,student_id,first_name,last_name', 'module:id,module_id,module_name'])
                                         ->get()
                                         ->map(function ($assignment) {
                                             return [
                                                 'id' => $assignment->id,
                                                 'student_id' => $assignment->student->student_id,
                                                 'student_name' => $assignment->student->first_name . ' ' . $assignment->student->last_name,
                                                 'module_id' => $assignment->module->module_id,
                                                 'module_name' => $assignment->module->module_name
                                             ];
                                         });

            return response()->json([
                'success' => true,
                'data' => $assignments
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching module assignments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching module assignments.'
            ], 500);
        }
    }

    /**
     * Assign modules to students
     */
    public function assignModules(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.student_id' => 'required|exists:students,student_id',
            'assignments.*.module_id' => 'required|exists:modules,module_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'course_id' => 'required|exists:courses,course_id',
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'semester' => 'required|in:1,2,3,4,5,6'
        ]);

        try {
            DB::beginTransaction();

            // Delete existing assignments for this intake and semester
            ModuleManagement::where('intake_id', $request->intake_id)
                          ->where('semester', $request->semester)
                          ->delete();

            // Create new assignments
            $assignments = [];
            foreach ($request->assignments as $assignment) {
                $assignments[] = [
                    'student_id' => $assignment['student_id'],
                    'module_id' => $assignment['module_id'],
                    'intake_id' => $request->intake_id,
                    'course_id' => $request->course_id,
                    'location' => $request->location,
                    'semester' => $request->semester,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            ModuleManagement::insert($assignments);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Modules assigned successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning modules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error assigning modules.'
            ], 500);
        }
    }

    /**
     * Get current elective module registrations
     */
    public function getElectiveRegistrations(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya'
        ]);

        try {
            $semester = Semester::find($request->semester_id);
            
            $registrations = ModuleManagement::where('intake_id', $request->intake_id)
                                           ->where('course_id', $request->course_id)
                                           ->where('location', $request->location)
                                           ->where('semester', $semester->name)
                                           ->whereHas('module', function($query) use ($request) {
                                               $query->whereHas('courses', function($q) use ($request) {
                                                   $q->where('course_id', $request->course_id)
                                                     ->where('is_core', false);
                                               });
                                           })
                                           ->with(['student:id,student_id,first_name,last_name', 'module:id,module_id,module_name'])
                                           ->get()
                                           ->map(function ($registration) {
                                               return [
                                                   'id' => $registration->id,
                                                   'student_id' => $registration->student->student_id,
                                                   'student_name' => $registration->student->first_name . ' ' . $registration->student->last_name,
                                                   'module_id' => $registration->module->module_id,
                                                   'module_name' => $registration->module->module_name
                                               ];
                                           });

            return response()->json([
                'success' => true,
                'data' => $registrations
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching elective registrations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching elective registrations.'
            ], 500);
        }
    }

    /**
     * Remove a module assignment
     */
    public function removeAssignment(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'assignment_id' => 'required|exists:module_management,id'
        ]);

        try {
            $assignment = ModuleManagement::find($request->assignment_id);
            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found.'
                ], 404);
            }

            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment removed successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing assignment.'
            ], 500);
        }
    }

    /**
     * Get module statistics
     */
    public function getModuleStatistics(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        try {
            $statistics = [
                'total_assignments' => ModuleManagement::count(),
                'assignments_by_location' => ModuleManagement::getStudentCountByLocation(),
                'assignments_by_semester' => ModuleManagement::getStudentCountBySemester(),
                'assignments_by_course' => ModuleManagement::getStudentCountByCourse(),
                'assignments_by_module' => ModuleManagement::getStudentCountByModule()
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching module statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching module statistics.'
            ], 500);
        }
    }

    /**
     * Get ongoing semesters for elective module registration
     */
    public function getOngoingSemesters(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya'
        ]);

        try {
            $semesters = Semester::where('course_id', $request->course_id)
                                ->where('intake_id', $request->intake_id)
                                ->whereIn('status', ['active', 'upcoming']) // Show both ongoing and upcoming semesters
                                ->orderBy('name')
                                ->get()
                                ->map(function($semester) {
                                    // Get elective modules for this semester from semester_module table
                                    $electiveModules = \DB::table('modules')
                                        ->join('semester_module', 'modules.module_id', '=', 'semester_module.module_id')
                                        ->where('semester_module.semester_id', $semester->id)
                                        ->where('modules.module_type', 'Elective') // Only elective modules
                                        ->select('modules.module_id', 'modules.module_name', 'modules.module_type', 'modules.credits')
                                        ->orderBy('modules.module_name')
                                        ->get();

                                    return [
                                        'id' => $semester->id,
                                        'name' => $semester->name,
                                        'status' => $semester->status,
                                        'elective_modules' => $electiveModules
                                    ];
                                });

            return response()->json([
                'success' => true,
                'data' => $semesters
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ongoing semesters: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching ongoing semesters.'
            ], 500);
        }
    }

    /**
     * Get eligible students for elective module registration
     */
    public function getElectiveStudents(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester_id' => 'required|exists:semesters,id',
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya'
        ]);

        try {
            // Debug: Log the request parameters
            \Log::info('getElectiveStudents called with:', [
                'course_id' => $request->course_id,
                'intake_id' => $request->intake_id,
                'semester_id' => $request->semester_id,
                'location' => $request->location
            ]);
            
            // Get students who have registered for this semester through semester registration
            $students = \App\Models\SemesterRegistration::where('course_id', $request->course_id)
                ->where('intake_id', $request->intake_id)
                ->where('semester_id', $request->semester_id)
                ->where('location', $request->location)
                ->where('status', 'registered')
                ->with('student')
                ->get();
            
            // Debug: Log the query results
            \Log::info('SemesterRegistration query results:', [
                'count' => $students->count(),
                'registrations' => $students->toArray()
            ]);
            
            $mappedStudents = $students->map(function($reg) {
                return [
                    'student_id' => $reg->student->student_id,
                    'name' => $reg->student->name_with_initials,
                    'email' => $reg->student->email,
                    'nic' => $reg->student->id_value,
                ];
            });

            return response()->json([
                'success' => true,
                'students' => $mappedStudents
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching elective students: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching eligible students.'
            ], 500);
        }
    }

    /**
     * Get elective modules for a specific semester
     */
    public function getElectiveModules(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'course_id' => 'required|exists:courses,course_id'
        ]);

        try {
            // Get modules assigned to this specific semester from semester_module table
            $electiveModules = \DB::table('modules')
                ->join('semester_module', 'modules.module_id', '=', 'semester_module.module_id')
                ->where('semester_module.semester_id', $request->semester_id)
                ->where('modules.module_type', 'Elective') // Only elective modules
                ->select('modules.module_id', 'modules.module_name', 'modules.module_type', 'modules.credits')
                ->orderBy('modules.module_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $electiveModules
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching elective modules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching elective modules.'
            ], 500);
        }
    }

    /**
     * Register students for elective modules
     */
    public function registerElectiveModules(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        // Debug: Log the incoming request data
        \Log::info('Elective module registration request data:', $request->all());

        $request->validate([
            'register_students' => 'required|array|min:1',
            'register_students.*' => 'exists:students,student_id',
            'semester_id' => 'required|exists:semesters,id',
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'module_id' => 'required|exists:modules,module_id',
            'specialization' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $semester = Semester::find($request->semester_id);
            if (!$semester) {
                \Log::error('Semester not found:', ['semester_id' => $request->semester_id]);
                return response()->json([
                    'success' => false, 
                    'message' => '❌ Invalid semester selected. Please try again.'
                ], 400);
            }

            \Log::info('Found semester:', ['semester' => $semester->toArray()]);

            $registrations = [];
            $alreadyRegistered = 0;
            foreach ($request->register_students as $studentId) {
                // Check if student is already registered for this module in this semester
                $existing = ModuleManagement::where('student_id', $studentId)
                                          ->where('module_id', $request->module_id)
                                          ->where('semester', $semester->name)
                                          ->where('course_id', $request->course_id)
                                          ->where('intake_id', $request->intake_id)
                                          ->where('location', $request->location)
                                          ->exists();

                if (!$existing) {
                    $registrations[] = [
                        'student_id' => $studentId,
                        'module_id' => $request->module_id,
                        'intake_id' => $request->intake_id,
                        'course_id' => $request->course_id,
                        'location' => $request->location,
                        'specialization' => $request->specialization,
                        'semester' => $semester->name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                } else {
                    $alreadyRegistered++;
                    \Log::info('Student already registered:', ['student_id' => $studentId, 'semester' => $semester->name, 'module_id' => $request->module_id]);
                }
            }

            \Log::info('Registrations to be created:', ['count' => count($registrations), 'data' => $registrations]);

            if (!empty($registrations)) {
                ModuleManagement::insert($registrations);
                \Log::info('Registrations saved successfully to module_management table');
            } else {
                \Log::info('No new registrations to save');
            }

            DB::commit();

            // Get module name for the success message
            $module = \DB::table('modules')->where('module_id', $request->module_id)->first();
            $moduleName = $module ? $module->module_name : 'Elective Module';

            $successMessage = '';
            if (count($registrations) > 0) {
                $successMessage = "🎉 **Success!** " . count($registrations) . " students have been successfully registered for **{$moduleName}** in Semester {$semester->name}!";
                
                if ($alreadyRegistered > 0) {
                    $successMessage .= " ({$alreadyRegistered} students were already registered)";
                }
            } else {
                $successMessage = "ℹ️ **Info:** All selected students are already registered for **{$moduleName}** in Semester {$semester->name}.";
            }

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'registrations_count' => count($registrations),
                'already_registered_count' => $alreadyRegistered
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error registering elective modules: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => '❌ **Oops!** Something went wrong while registering elective modules. Please try again or contact support if the issue persists.'
            ], 500);
        }
    }
}