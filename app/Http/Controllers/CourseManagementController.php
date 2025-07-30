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
    public function showCourseManagement()
    {
        $courses = Course::with('modules')->orderBy('course_name', 'asc')->get();
        $modules = Module::orderBy('module_name', 'asc')->get();
        return view('course_management', compact('courses', 'modules'));
    }

    public function storeCourseData(Request $request)
    {
        $validatedData = $request->validate([
            'location' => ['required', Rule::in(['Welisara', 'Moratuwa', 'Peradeniya'])],
            'course_type' => ['required', Rule::in(['degree', 'certificate'])],
            'course_name' => 'required|string|max:255|unique:courses,course_name',
            'no_of_semesters' => 'required_if:course_type,degree|nullable|integer|min:1',
            'duration_years' => 'required|integer|min:0',
            'duration_months' => 'required|integer|min:0|max:11',
            'duration_days' => 'required|integer|min:0|max:30',
            'min_credits' => 'required_if:course_type,degree|nullable|integer|min:1',
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
            // Combine duration fields into a string (e.g., '3-0-0')
            $courseData['duration'] = $request->duration_years . '-' . $request->duration_months . '-' . $request->duration_days;
            unset($courseData['duration_years'], $courseData['duration_months'], $courseData['duration_days']);
            
            // Combine training period fields into a string (e.g., '1-6-0')
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
            // Save specializations if degree
            if ($request->course_type === 'degree') {
                $specializations = $request->input('specializations', []);
                \Log::info('Specializations received in storeCourseData:', ['specializations' => $specializations]);
                $courseData['specializations'] = !empty($specializations) ? json_encode(array_filter($specializations)) : null;
                \Log::info('Specializations processed in storeCourseData:', ['specializations' => $courseData['specializations']]);
            } else {
                $courseData['specializations'] = null;
            }

            $courseData['added_by'] = Auth::id();
            $course = Course::create($courseData);

            DB::commit();

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
     * Get a course by its ID (API for edit modal)
     */
    public function getCourseById($id)
    {
        $course = Course::with(['modules'])->find($id);
        if ($course) {
            return response()->json(['success' => true, 'course' => $course]);
        } else {
            return response()->json(['success' => false, 'message' => 'Course not found'], 404);
        }
    }

    /**
     * Delete a course by its ID (API)
     */
    public function deleteCourse($id)
    {
        $course = Course::find($id);
        if ($course) {
            try {
                // Detach modules first
                $course->modules()->detach();
                // Delete the course
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Course deleted successfully']);
            } catch (\Exception $e) {
                Log::error('Error deleting course: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'An error occurred while deleting the course.'], 500);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Course not found'], 404);
        }
    }

    /**
     * Update existing course data
     */
    public function updateCourseData(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found'], 404);
        }

        $validatedData = $request->validate([
            'location' => ['required', Rule::in(['Welisara', 'Moratuwa', 'Peradeniya'])],
            'course_type' => ['required', Rule::in(['degree', 'certificate'])],
            'course_name' => 'required|string|max:255|unique:courses,course_name,' . $id . ',course_id',
            'no_of_semesters' => 'required_if:course_type,degree|nullable|integer|min:1',
            'duration_years' => 'required|integer|min:0',
            'duration_months' => 'required|integer|min:0|max:11',
            'duration_days' => 'required|integer|min:0|max:30',
            'min_credits' => 'required_if:course_type,degree|nullable|integer|min:1',
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
            // Combine duration fields into a string (e.g., '3-0-0')
            $courseData['duration'] = $request->duration_years . '-' . $request->duration_months . '-' . $request->duration_days;
            unset($courseData['duration_years'], $courseData['duration_months'], $courseData['duration_days']);
            
            // Combine training period fields into a string (e.g., '1-6-0')
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
            // Save specializations if degree
            if ($request->course_type === 'degree') {
                $specializations = $request->input('specializations', []);
                \Log::info('Specializations received in updateCourseData:', ['specializations' => $specializations]);
                $courseData['specializations'] = !empty($specializations) ? json_encode(array_filter($specializations)) : null;
                \Log::info('Specializations processed in updateCourseData:', ['specializations' => $courseData['specializations']]);
            } else {
                $courseData['specializations'] = null;
            }
            $course->update($courseData);

            DB::commit();

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
