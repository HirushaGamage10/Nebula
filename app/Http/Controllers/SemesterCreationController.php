<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;

class SemesterCreationController extends Controller
{
    public function index()
    {
        $semesters = Semester::with(['course', 'intake', 'modules'])->get();
        // Removed: return view('semesters.index', compact('semesters'));
    }

    public function create()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        $modules = Module::all();
        return view('semester_creation', compact('courses', 'intakes', 'modules'));
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Semester creation request data:', $request->all());

        try {
            // Map the form field 'semester' to 'name' for the database
            if ($request->has('semester')) {
                $request->merge(['name' => $request->semester]);
            }

            // Basic validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Only keep fillable fields for the Semester model
            $semesterData = collect($validated)->only([
                'name', 'course_id', 'intake_id', 'start_date', 'end_date'
            ])->toArray();

            // Determine status based on dates
            $today = now()->toDateString();
            if ($semesterData['start_date'] > $today) {
                $status = 'upcoming';
            } elseif ($semesterData['start_date'] <= $today && $semesterData['end_date'] >= $today) {
                $status = 'active';
            } else {
                $status = 'completed';
            }
            $semesterData['status'] = $status;

            \Log::info('Final semester data:', $semesterData);

            // Create the semester
            $semester = Semester::create($semesterData);
            
            \Log::info('Semester created successfully:', ['semester_id' => $semester->id]);

            // Handle modules if present - save to semester_module table
            $modules = $request->modules;
            if (!empty($modules) && is_array($modules)) {
                $semesterModuleData = [];
                foreach ($modules as $module) {
                    if (isset($module['module_id'])) {
                        $semesterModuleData[] = [
                            'semester_id' => $semester->id,
                            'module_id' => $module['module_id'],
                            'specialization' => $module['specialization'] ?? null
                        ];
                    }
                }
                
                if (!empty($semesterModuleData)) {
                    \DB::table('semester_module')->insert($semesterModuleData);
                    \Log::info('Modules saved to semester_module table:', ['count' => count($semesterModuleData)]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Semester created successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating semester:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the semester.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFilteredModules(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'location' => 'required|string',
            'intake_id' => 'required|exists:intakes,intake_id',
            'semester' => 'required|integer',
        ]);

        try {
            // Get all modules since course_modules table is empty and modules are assigned during semester creation
            $modules = \DB::table('modules')
                ->select('module_id', 'module_name', 'module_type', 'credits')
                ->orderBy('module_name')
                ->get()
                ->map(function($module) {
                    return [
                        'module_id' => $module->module_id,
                        'module_name' => $module->module_name,
                        'module_type' => $module->module_type,
                        'credits' => $module->credits,
                    ];
                });

            return response()->json(['modules' => $modules]);
        } catch (\Exception $e) {
            \Log::error('Error fetching modules: ' . $e->getMessage());
            return response()->json(['modules' => []]);
        }
    }

    public function getCoursesByLocation(Request $request)
    {
        $location = $request->query('location');
        $courses = \App\Models\Course::select('course_id', 'course_name')
            ->where('location', $location)
            ->where('course_type', 'degree')
            ->orderBy('course_name', 'asc')
            ->get();
        return response()->json(['success' => true, 'courses' => $courses]);
    }
}
