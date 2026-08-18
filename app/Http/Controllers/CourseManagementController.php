<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CourseManagementController extends Controller
{
    /**
     * Display the course management page with all courses and modules.
     */
    public function showCourseManagement()
    {
        $courses = Course::with('modules')->orderBy('course_name', 'asc')->get();
        $modules = Module::orderBy('module_name', 'asc')->get();
        return view('courses_&_modules.course_management', compact('courses', 'modules'));
    }

    /**
     * Store a new course record.
     *
     * Validates request data, normalizes duration and training values,
     * and saves the course in a transaction.
     */
    public function storeCourseData(Request $request)
    {
        $validatedData = $request->validate([
            'location' => ['required', Rule::in(['Welisara', 'Moratuwa', 'Peradeniya'])],
            'course_type' => ['required', Rule::in(['degree', 'diploma', 'certificate'])],
            'semester_format' => [
                'nullable',
                Rule::requiredIf(in_array($request->course_type, ['degree', 'diploma'], true)),
                Rule::in(['numerical', 'alphabetical'])
            ],
            'course_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses')->where(function ($query) use ($request) {
                    return $query->where('location', $request->location);
                })
            ],
            'no_of_semesters' => 'required_if:course_type,degree,required_if:course_type,diploma|nullable|integer|min:1',
            'duration_years' => 'required|integer|min:0',
            'duration_months' => 'required|integer|min:0|max:11',
            'duration_days' => 'required|integer|min:0|max:30',
            'min_credits' => 'required_if:course_type,degree,required_if:course_type,diploma|nullable|integer|min:1',
            'entry_qualification' => 'required|string',
            'conducted_by' => 'required|string|max:255',
            'course_medium' => ['required', Rule::in(['Sinhala', 'English'])],
            'training_years' => 'nullable|integer|min:0',
            'training_months' => 'nullable|integer|min:0|max:11',
            'training_days' => 'nullable|integer|min:0|max:30',
            'course_content' => 'required_if:course_type,certificate|nullable|string',
            'specializations' => 'nullable|array',
            'specializations.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $courseData = $request->except('modules');

            // Normalize duration fields into a single stored string.
            $courseData['duration'] = $request->duration_years . '-' . $request->duration_months . '-' . $request->duration_days;
            unset($courseData['duration_years'], $courseData['duration_months'], $courseData['duration_days']);

            // Combine training period
            if ($request->has('training_years') || $request->has('training_months') || $request->has('training_days')) {
                $trainingYears = $request->training_years ?? 0;
                $trainingMonths = $request->training_months ?? 0;
                $trainingDays = $request->training_days ?? 0;
                $courseData['training_period'] = $trainingYears . '-' . $trainingMonths . '-' . $trainingDays;
            }
            unset($courseData['training_years'], $courseData['training_months'], $courseData['training_days']);

            // Nullify fields for certificate
            if ($request->course_type === 'certificate') {
                $courseData['no_of_semesters'] = null;
                $courseData['min_credits'] = null;
            }

            // Handle specializations for degree and diploma courses
            if (in_array($request->course_type, ['degree', 'diploma'], true)) {
                $specializations = $request->input('specializations', []);
                $courseData['specializations'] = !empty($specializations)
                    ? json_encode(array_filter($specializations))
                    : null;
            } else {
                $courseData['specializations'] = null;
            }

            $courseData['added_by'] = Auth::id();
            $course = Course::create($courseData);

            DB::commit();

            $durationParts = explode('-', $course->duration);
            $course->duration = [
                'years' => (int)($durationParts[0] ?? 0),
                'months' => (int)($durationParts[1] ?? 0),
                'days' => (int)($durationParts[2] ?? 0)
            ];

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully.',
                'course' => $course
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing course data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the course.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a course by ID and return its structured data.
     *
     * This converts stored duration and training period strings back into arrays.
     */
    public function getCourseById($id)
    {
        $course = Course::with(['modules'])->find($id);
        if ($course) {
            $durationParts = explode('-', $course->duration);
            $course->duration = [
                'years' => (int)($durationParts[0] ?? 0),
                'months' => (int)($durationParts[1] ?? 0),
                'days' => (int)($durationParts[2] ?? 0)
            ];

            if ($course->training_period) {
                $trainingParts = explode('-', $course->training_period);
                $course->training_period = [
                    'years' => (int)($trainingParts[0] ?? 0),
                    'months' => (int)($trainingParts[1] ?? 0),
                    'days' => (int)($trainingParts[2] ?? 0)
                ];
            }

            return response()->json(['success' => true, 'course' => $course]);
        }
        return response()->json(['success' => false, 'message' => 'Course not found'], 404);
    }

    /**
     * Delete a course and detach its related modules.
     */
    public function deleteCourse($id)
    {
        $course = Course::find($id);
        if ($course) {
            try {
                $course->modules()->detach();
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Course deleted successfully']);
            } catch (\Exception $e) {
                Log::error('Error deleting course: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'An error occurred while deleting the course.'], 500);
            }
        }
        return response()->json(['success' => false, 'message' => 'Course not found'], 404);
    }

    /**
     * Update an existing course and keep related intake names in sync.
     */
    public function updateCourseData(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found'], 404);
        }

        $validatedData = $request->validate([
            'location' => ['required', Rule::in(['Welisara', 'Moratuwa', 'Peradeniya'])],
            'course_type' => ['required', Rule::in(['degree', 'diploma', 'certificate'])],
            'semester_format' => [
                'nullable',
                Rule::requiredIf(in_array($request->course_type, ['degree', 'diploma'], true)),
                Rule::in(['numerical', 'alphabetical'])
            ],
            'course_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses')->where(function ($query) use ($request) {
                    return $query->where('location', $request->location);
                })->ignore($id, 'course_id')
            ],
            'no_of_semesters' => 'required_if:course_type,degree,required_if:course_type,diploma|nullable|integer|min:1',
            'duration_years' => 'required|integer|min:0',
            'duration_months' => 'required|integer|min:0|max:11',
            'duration_days' => 'required|integer|min:0|max:30',
            'min_credits' => 'required_if:course_type,degree,required_if:course_type,diploma|nullable|integer|min:1',
            'entry_qualification' => 'required|string',
            'conducted_by' => 'required|string|max:255',
            'course_medium' => ['required', Rule::in(['Sinhala', 'English'])],
            'training_years' => 'nullable|integer|min:0',
            'training_months' => 'nullable|integer|min:0|max:11',
            'training_days' => 'nullable|integer|min:0|max:30',
            'course_content' => 'required_if:course_type,certificate|nullable|string',
            'specializations' => 'nullable|array',
            'specializations.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $courseData = $request->except('modules');

            $courseData['duration'] = $request->duration_years . '-' . $request->duration_months . '-' . $request->duration_days;
            unset($courseData['duration_years'], $courseData['duration_months'], $courseData['duration_days']);

            if ($request->has('training_years') || $request->has('training_months') || $request->has('training_days')) {
                $trainingYears = $request->training_years ?? 0;
                $trainingMonths = $request->training_months ?? 0;
                $trainingDays = $request->training_days ?? 0;
                $courseData['training_period'] = $trainingYears . '-' . $trainingMonths . '-' . $trainingDays;
            }
            unset($courseData['training_years'], $courseData['training_months'], $courseData['training_days']);

            if ($request->course_type === 'certificate') {
                $courseData['no_of_semesters'] = null;
                $courseData['min_credits'] = null;
            }

            if (in_array($request->course_type, ['degree', 'diploma'], true)) {
                $specializations = $request->input('specializations', []);
                $courseData['specializations'] = !empty($specializations)
                    ? json_encode(array_filter($specializations))
                    : null;
            } else {
                $courseData['specializations'] = null;
            }

            $course->update($courseData);

            // Keep intakes.course_name in sync if the course name changed,
            // so legacy name-based lookups continue to work after a rename.
            if ($course->wasChanged('course_name')) {
                \App\Models\Intake::where('course_id', $course->course_id)
                    ->update(array_merge(
                        ['course_name' => $course->course_name],
                        \App\Support\UserTrackingData::forUpdate()
                    ));
            }

            DB::commit();

            $durationParts = explode('-', $course->duration);
            $course->duration = [
                'years' => (int)($durationParts[0] ?? 0),
                'months' => (int)($durationParts[1] ?? 0),
                'days' => (int)($durationParts[2] ?? 0)
            ];

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully.',
                'course' => $course
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating course data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the course.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}