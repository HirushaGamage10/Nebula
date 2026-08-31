<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;
use App\Support\SemesterModuleSpecializationHelper;
use Illuminate\Support\Facades\DB;

class SemesterCreationController extends Controller
{
    public function index()
    {
        $semesters = Semester::with(['course', 'intake', 'modules'])->orderBy('created_at', 'desc')->get();
        $courses = Course::orderBy('course_name', 'asc')->get();
        return view('courses_&_modules.semester_index', compact('semesters', 'courses'));
    }

    public function create()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        $modules = Module::all();
        return view('courses_&_modules.semester_creation', compact('courses', 'intakes', 'modules'));
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
        
        return view('courses_&_modules.semester_edit', compact('semester', 'courses', 'intakes', 'modules', 'semesterModules'));
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
                $this->syncSemesterModules($semester->id, $modules);
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
                'modules.*.specializations' => 'nullable|array',
                'modules.*.specializations.*' => 'string|max:255',
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
                $this->syncSemesterModules($semester->id, $modules);
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
            'course_id'  => 'required|exists:courses,course_id',
            'location'   => 'required|string',
            'intake_id'  => 'required|exists:intakes,intake_id',
            'semester'   => 'required|integer|exists:semesters,id',
        ]);

        $courseId  = (int) $request->course_id;
        $intakeId  = (int) $request->intake_id;
        $semesterId = (int) $request->semester;

        try {
<<<<<<< HEAD
            $course = Course::find($request->course_id);
            $intake = Intake::find($request->intake_id);
=======
            // Validate that the requested semester belongs to the given course and intake,
            // preventing cross-cohort module leakage.
            $semester = \DB::table('semesters')
                ->where('id', $semesterId)
                ->where('course_id', $courseId)
                ->where('intake_id', $intakeId)
                ->first();

            if (!$semester) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected semester does not belong to the specified course and intake.',
                    'modules' => [],
                ], 422);
            }

            // Primary path: retrieve modules assigned to this specific semester via semester_module.
            // This is the authoritative source for cohort-specific module lists.
            $modules = \DB::table('modules')
                ->join('semester_module', 'modules.module_id', '=', 'semester_module.module_id')
                ->where('semester_module.semester_id', $semesterId)
                ->select(
                    'modules.module_id',
                    'modules.module_name',
                    'modules.module_code',
                    'modules.module_type',
                    'modules.credits'
                )
                ->orderBy('modules.module_name')
                ->distinct()
                ->get()
                ->map(fn ($m) => [
                    'module_id'   => $m->module_id,
                    'module_name' => $m->module_name,
                    'module_code' => $m->module_code,
                    'module_type' => $m->module_type,
                    'credits'     => $m->credits,
                ]);

            // Fallback: if no semester_module rows exist yet for this semester, fall back to
            // course_modules filtered by course_id (and semester number when populated).
            if ($modules->isEmpty()) {
                $modules = \DB::table('modules')
                    ->join('course_modules', 'modules.module_id', '=', 'course_modules.module_id')
                    ->where('course_modules.course_id', $courseId)
                    ->select(
                        'modules.module_id',
                        'modules.module_name',
                        'modules.module_code',
                        'modules.module_type',
                        'modules.credits'
                    )
                    ->orderBy('modules.module_name')
                    ->distinct()
                    ->get()
                    ->map(fn ($m) => [
                        'module_id'   => $m->module_id,
                        'module_name' => $m->module_name,
                        'module_code' => $m->module_code,
                        'module_type' => $m->module_type,
                        'credits'     => $m->credits,
                    ]);
            }
>>>>>>> b343af1de3186ae1c793d003e14b57727d3855fd

            if (!$course || !$intake) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course or Intake not found.',
                    'modules' => []
                ], 422);
            }

            // Validate that the selected intake belongs to the course/location
            $belongsToCourse = ($intake->course_id == $course->course_id) || (is_null($intake->course_id) && $intake->course_name === $course->course_name);
            if (!$belongsToCourse || $intake->location !== $request->location) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected intake does not belong to the chosen course or location.',
                    'modules' => []
                ], 422);
            }

            // Retrieve modules through course/intake/semester assignment tables
            if ($course->course_type === 'certificate') {
                $modules = \DB::table('modules')
                    ->join('intake_modules', 'modules.module_id', '=', 'intake_modules.module_id')
                    ->where('intake_modules.intake_id', $intake->intake_id)
                    ->select('modules.module_id', 'modules.module_name', 'modules.module_code', 'modules.module_type', 'modules.credits')
                    ->orderBy('modules.module_name')
                    ->get();
            } else {
                $modules = \DB::table('modules')
                    ->join('course_modules', 'modules.module_id', '=', 'course_modules.module_id')
                    ->where('course_modules.course_id', $course->course_id)
                    ->where('course_modules.semester', $request->semester)
                    ->select('modules.module_id', 'modules.module_name', 'modules.module_code', 'modules.module_type', 'modules.credits')
                    ->orderBy('modules.module_name')
                    ->get();
            }

            $formattedModules = $modules->map(function ($module) {
                return [
                    'module_id'   => $module->module_id,
                    'module_name' => $module->module_name,
                    'module_code' => $module->module_code,
                    'module_type' => $module->module_type,
                    'credits'     => $module->credits,
                ];
            });

            return response()->json(['modules' => $formattedModules]);

        } catch (\Exception $e) {
            \Log::error('Error fetching filtered modules: ' . $e->getMessage());
            return response()->json(['modules' => []]);
        }
    }



    public function getCoursesByLocation(Request $request)
    {
        $location = $request->query('location');
        $courses = \App\Models\Course::select('course_id', 'course_name')
            ->where('location', $location)
            ->whereIn('course_type', ['degree', 'diploma'])
            ->orderBy('course_name', 'asc')
            ->get();
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function getIntakesForCourseAndLocation($courseId, $location)
    {
        try {
            $course = Course::select('course_id', 'course_name')->find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'intakes' => [],
                    'message' => 'Course not found.'
                ], 404);
            }

            $intakes = Intake::query()
                ->where('location', trim($location))
                ->where(function ($query) use ($course) {
                    $query->where('course_id', $course->course_id)
                        // Fallback keeps old records (created before course_id linkage) working.
                        ->orWhere(function ($fallback) use ($course) {
                            $fallback->whereNull('course_id')
                                ->where('course_name', $course->course_name);
                        });
                })
                ->orderBy('batch', 'asc')
                ->get(['intake_id', 'batch']);

            return response()->json([
                'success' => true,
                'intakes' => $intakes
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching intakes for semester creation:', [
                'course_id' => $courseId,
                'location' => $location,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'intakes' => [],
                'message' => 'Failed to load intakes.'
            ], 500);
        }
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
            Semester::whereIn('id', $semesterIds)->update(array_merge(
                ['status' => $status],
                \App\Support\UserTrackingData::forUpdate()
            ));

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
                    'specialization' => $module->specialization,
                    'specializations' => $module->specializations ?? null,
                    ...\App\Support\UserTrackingData::forCreate(),
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

    private function syncSemesterModules(int $semesterId, array $modules): void
    {
        $desiredModuleIds = [];

        foreach ($modules as $module) {
            if (!isset($module['module_id'])) {
                continue;
            }

            $normalized = SemesterModuleSpecializationHelper::normalizePayload($module);
            if ($normalized['module_id'] === '') {
                continue;
            }

            $desiredModuleIds[] = $normalized['module_id'];

            DB::table('semester_module')->updateOrInsert(
                [
                    'semester_id' => $semesterId,
                    'module_id' => $normalized['module_id'],
                ],
                [
                    'specializations' => $normalized['specializations'],
                    'specialization' => $normalized['specialization'],
                    ...\App\Support\UserTrackingData::forUpdate(),
                ]
            );
        }

        if (empty($desiredModuleIds)) {
            DB::table('semester_module')->where('semester_id', $semesterId)->delete();
            return;
        }

        DB::table('semester_module')
            ->where('semester_id', $semesterId)
            ->whereNotIn('module_id', $desiredModuleIds)
            ->delete();
    }
}
