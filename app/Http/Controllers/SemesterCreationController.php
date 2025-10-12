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
        $semesters = Semester::with(['course', 'intake', 'modules'])->orderBy('created_at', 'desc')->get();
        $courses = Course::orderBy('course_name', 'asc')->get();
        return view('semester_index', compact('semesters', 'courses'));
    }

    public function create()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        $modules = Module::all();
        return view('semester_creation', compact('courses', 'intakes', 'modules'));
    }

    public function edit(Semester $semester)
    {
        $semester->load(['course', 'intake', 'modules']);
        $courses = Course::all();
        $intakes = Intake::all();
        $modules = Module::all();
        
        // Get the semester's modules with specializations
        $semesterModules = \DB::table('semester_module')
            ->where('semester_id', $semester->id)
            ->get();
        
        return view('semester_edit', compact('semester', 'courses', 'intakes', 'modules', 'semesterModules'));
    }

    public function update(Request $request, Semester $semester)
    {
        \Log::info('Semester update request data:', $request->all());

        try {
            // Handle JSON requests
            if ($request->isJson()) {
                $data = $request->json()->all();
                $request->merge($data);
            }
            
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

            \Log::info('Final semester update data:', $semesterData);

            // Update the semester
            $semester->update($semesterData);
            
            \Log::info('Semester updated successfully:', ['semester_id' => $semester->id]);

            // Handle modules if present - update semester_module table
            $modules = $request->modules;
            if (is_array($modules)) {
                // Delete existing modules
                \DB::table('semester_module')->where('semester_id', $semester->id)->delete();
                
                // Insert new modules
                if (!empty($modules)) {
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
                        \Log::info('Modules updated in semester_module table:', ['count' => count($semesterModuleData)]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Semester updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating semester:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the semester.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Semester $semester)
    {
        try {
            // Delete associated modules first
            \DB::table('semester_module')->where('semester_id', $semester->id)->delete();
            
            // Delete the semester
            $semester->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Semester deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting semester:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the semester.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Semester creation request data:', $request->all());

        try {
            // Handle JSON requests
            if ($request->isJson()) {
                $data = $request->json()->all();
                $request->merge($data);
            }
            
            // Map the form field 'semester' to 'name' for the database
            if ($request->has('semester')) {
                $request->merge(['name' => $request->semester]);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,course_id',
                'intake_id' => 'required|exists:intakes,intake_id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'modules' => 'required|array',
                'modules.*.module_id' => 'required|exists:modules,module_id',
                'modules.*.specialization' => 'nullable|string|max:255',
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
            $modules = $request->input('modules', []);
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
                \Log::info('Semester module insert:', $semesterModuleData);
                if (!empty($semesterModuleData)) {
                    \DB::table('semester_module')->insert($semesterModuleData);
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
            // Fetch all modules with module_code included
            $modules = \DB::table('modules')
                ->select('module_id', 'module_name', 'module_code', 'module_type', 'credits')
                ->orderBy('module_name')
                ->get()
                ->map(function ($module) {
                    return [
                        'module_id'   => $module->module_id,
                        'module_name' => $module->module_name,
                        'module_code' => $module->module_code, // ✅ ensure module_code is included
                        'module_type' => $module->module_type,
                        'credits'     => $module->credits,
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

    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'semester_ids' => 'required|array',
                'semester_ids.*' => 'exists:semesters,id',
                'status' => 'required|in:upcoming,active,completed'
            ]);

            $semesterIds = $request->semester_ids;
            $status = $request->status;

            // Update semesters
            Semester::whereIn('id', $semesterIds)->update(['status' => $status]);

            $updatedCount = count($semesterIds);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated status for {$updatedCount} semester(s)."
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in bulk status update:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating semester statuses.'
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'semester_ids' => 'required|array',
                'semester_ids.*' => 'exists:semesters,id'
            ]);

            $semesterIds = $request->semester_ids;

            // Delete associated modules first
            \DB::table('semester_module')->whereIn('semester_id', $semesterIds)->delete();
            
            // Delete semesters
            Semester::whereIn('id', $semesterIds)->delete();

            $deletedCount = count($semesterIds);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} semester(s)."
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in bulk delete:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting semesters.'
            ], 500);
        }
    }

    public function duplicateSemester(Request $request, Semester $semester)
    {
        try {
            $request->validate([
                'new_name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Create new semester
            $newSemester = $semester->replicate();
            $newSemester->name = $request->new_name;
            $newSemester->start_date = $request->start_date;
            $newSemester->end_date = $request->end_date;
            
            // Determine status based on dates
            $today = now()->toDateString();
            if ($newSemester->start_date > $today) {
                $newSemester->status = 'upcoming';
            } elseif ($newSemester->start_date <= $today && $newSemester->end_date >= $today) {
                $newSemester->status = 'active';
            } else {
                $newSemester->status = 'completed';
            }
            
            $newSemester->save();

            // Copy modules
            $semesterModules = \DB::table('semester_module')
                ->where('semester_id', $semester->id)
                ->get();

            foreach ($semesterModules as $module) {
                \DB::table('semester_module')->insert([
                    'semester_id' => $newSemester->id,
                    'module_id' => $module->module_id,
                    'specialization' => $module->specialization
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Semester duplicated successfully.',
                'semester_id' => $newSemester->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error duplicating semester:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while duplicating the semester.'
            ], 500);
        }
    }
}
