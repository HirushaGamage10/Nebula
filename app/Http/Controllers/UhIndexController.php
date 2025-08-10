<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Intake;
use App\Models\CourseRegistration;

class UhIndexController extends Controller
{
    // Page
    public function showPage()
    {
        return view('uh_index_numbers');
    }

    // Courses by location
    public function getCoursesByLocation(Request $request)
    {
        try {
            $courses = Course::where('location', $request->input('location'))
                ->get(['course_id', 'course_name']);
            return response()->json(['courses' => $courses]);
        } catch (\Throwable $e) {
            Log::error('getCoursesByLocation: ' . $e->getMessage());
            return response()->json(['courses' => [], 'error' => 'Failed to fetch courses']);
        }
    }

    // Intakes by course
    public function getIntakesByCourse(Request $request)
    {
        try {
            $course = Course::find($request->input('course_id'));
            if (!$course) return response()->json(['intakes' => []]);

            $intakes = Intake::where('course_name', $course->course_name)
                ->get(['intake_id', 'batch']);
            return response()->json(['intakes' => $intakes]);
        } catch (\Throwable $e) {
            Log::error('getIntakesByCourse: ' . $e->getMessage());
            return response()->json(['intakes' => [], 'error' => 'Failed to fetch intakes']);
        }
    }

    public function getStudentsByIntake(Request $request)
    {
        try {
            $intakeId = (int) $request->input('intake_id');

            // Only students who are registered in SR for this intake
            $rows = \DB::table('semester_registrations as sr')
                ->join('students as s', 's.student_id', '=', 'sr.student_id')
                // IMPORTANT: table name is singular in your DB: course_registration
                ->leftJoin('course_registration as cr', function ($j) use ($intakeId) {
                    $j->on('cr.student_id', '=', 'sr.student_id')
                        ->where('cr.intake_id', '=', $intakeId);
                })
                ->where('sr.intake_id', $intakeId)
                ->where('sr.status', 'registered') // matches your enum value
                ->select([
                    'sr.student_id',
                    'sr.intake_id',
                    's.full_name as name',
                    \DB::raw('COALESCE(cr.uh_index_number, "") as uh_index_number'),
                ])
                ->distinct()
                ->get();

            \Log::info('UH students fetched', ['intake_id' => $intakeId, 'count' => $rows->count()]);

            return response()->json(['students' => $rows]);
        } catch (\Throwable $e) {
            \Log::error('getStudentsByIntake error: ' . $e->getMessage());
            return response()->json(['students' => [], 'error' => 'Failed to fetch students']);
        }
    }

    // Save External/UH IDs
    public function saveUhIndexNumbers(Request $request)
    {
        try {
            $request->validate([
                'students'                 => 'required|array',
                'students.*.student_id'    => 'required|string',
                'students.*.uh_index_number' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            $updated = 0;
            $errors = [];
            foreach ($request->input('students') as $row) {
                $reg = CourseRegistration::where('student_id', $row['student_id'])->first();
                if ($reg) {
                    $reg->update(['uh_index_number' => $row['uh_index_number'] ?? '']);
                    $updated++;
                } else {
                    $errors[] = "No course registration for student {$row['student_id']}";
                }
            }

            DB::commit();

            if ($errors) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some updates failed: ' . implode(', ', $errors),
                    'updated_count' => $updated,
                    'errors' => $errors
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "External institute student IDs saved. Updated {$updated} records.",
                'updated_count' => $updated
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data format.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('saveUhIndexNumbers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error while saving IDs.'
            ], 500);
        }
    }

    // NEW: Terminate a student for the intake (updates CourseRegistration.status)
    public function terminateStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'intake_id'  => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            // Make sure the student is actually registered in this intake
            $sr = \App\Models\SemesterRegistration::where('student_id', $request->student_id)
                ->where('intake_id', $request->intake_id)
                ->where('status', 'registered')   // your enum is lowercase
                ->lockForUpdate()
                ->first();

            if (!$sr) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not registered for this intake or already terminated.'
                ], 404);
            }

            // Terminate ALL SR rows for this intake (safe if there are multiple semesters)
            \App\Models\SemesterRegistration::where('student_id', $request->student_id)
                ->where('intake_id', $request->intake_id)
                ->update([
                    'status'          => 'terminated', // matches enum
                    'desired_status'  => null,
                    'approval_status' => 'none',
                    'updated_at'      => now(),
                ]);

            // DO NOT write an invalid enum to course_registration.status
            // If you want to touch the course_registration row, use the correct table name.
            // Example: just leave it as-is, or record a flag elsewhere.
            // DB::table('course_registration')
            //   ->where('student_id', $request->student_id)
            //   ->where('intake_id', $request->intake_id)
            //   ->update(['some_flag' => 1]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Student terminated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('terminateStudent error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to terminate student.'], 500);
        }
    }
}
