<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Intake;
use App\Models\SpecializationRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SpecializationRegistrationController extends Controller
{
    private function courseSpecializations(Course $course): array
    {
        $specializations = $course->specializations;
        for ($i = 0; $i < 2 && is_string($specializations); $i++) {
            $specializations = json_decode($specializations, true);
        }

        return is_array($specializations)
            ? array_values(array_filter($specializations, fn ($value) => is_string($value) && trim($value) !== ''))
            : [];
    }

    public function index()
    {
        return view('registration.specialization_registration', [
            'locations' => ['Welisara', 'Moratuwa', 'Peradeniya'],
        ]);
    }

    public function courses(Request $request)
    {
        return response()->json(['courses' => Course::where('location', $request->location)
            ->whereIn('course_type', ['degree', 'diploma'])
            ->orderBy('course_name')->get(['course_id', 'course_name', 'specializations'])]);
    }

    public function intakes(Request $request)
    {
        $course = Course::findOrFail($request->course_id);
        return response()->json(['intakes' => Intake::forCourse($course, $request->location)
            ->orderBy('batch')->get(['intake_id', 'batch'])]);
    }

    public function students(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'location' => 'required|string',
        ]);

        $registrations = CourseRegistration::query()
            ->where($data)
            ->where(fn ($query) => $query->where('status', 'Registered')->orWhere('approval_status', 'Approved by DGM'))
            ->with('student')
            ->get();

        $assignmentsByStudentId = collect();
        if (Schema::hasTable('specialization_registrations')) {
            $assignmentsByStudentId = SpecializationRegistration::query()
                ->where('course_id', $data['course_id'])
                ->where('intake_id', $data['intake_id'])
                ->where('location', $data['location'])
                ->where('status', 'registered')
                ->whereIn('student_id', $registrations->pluck('student_id')->all())
                ->get()
                ->keyBy('student_id');
        }

        $students = $registrations->map(function ($registration) use ($assignmentsByStudentId) {
                $student = $registration->student;
                if (!$student) {
                    return null;
                }

                $assignment = $assignmentsByStudentId->get($registration->student_id);

                return [
                    'student_id' => $registration->student_id,
                    'course_registration_id' => $registration->course_registration_id,
                    'name' => $student->name_with_initials,
                    'email' => $student->email,
                    'nic' => $student->id_value ?? $student->nic ?? null,
                    'specialization' => $assignment?->status === 'registered' ? $assignment->specialization : null,
                ];
            })
            ->filter()
            ->values();

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'intake_id' => 'required|exists:intakes,intake_id',
            'location' => 'required|string',
            'specialization' => 'required|string|max:255',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,student_id',
        ]);

        $course = Course::findOrFail($data['course_id']);
        abort_unless(in_array($course->course_type, ['degree', 'diploma'], true), 422, 'Specialization registration is only for degree and diploma courses.');
        abort_unless(in_array($data['specialization'], $this->courseSpecializations($course), true), 422, 'Invalid specialization for this course.');
        abort_if(!Schema::hasTable('specialization_registrations'), 422, 'Specialization assignments table is missing. Please run pending migrations.');

        $eligibleIds = CourseRegistration::where('course_id', $data['course_id'])->where('intake_id', $data['intake_id'])
            ->where('location', $data['location'])->whereIn('student_id', $data['student_ids'])
            ->where(fn ($query) => $query->where('status', 'Registered')->orWhere('approval_status', 'Approved by DGM'))
            ->pluck('student_id')->all();

        foreach ($eligibleIds as $studentId) {
            SpecializationRegistration::updateOrCreate(
                ['student_id' => $studentId, 'course_id' => $data['course_id'], 'intake_id' => $data['intake_id']],
                ['location' => $data['location'], 'specialization' => $data['specialization'], 'status' => 'registered']
            );
        }

        return response()->json(['success' => true, 'message' => count($eligibleIds) . ' student(s) registered for the specialization.']);
    }
}
