<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Intake;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for StudentViewController filter logic.
 *
 * Specifically guards against the two logic errors that were fixed:
 *   1. student_id search bypassing course/intake/status/location constraints
 *      (was: where(student_id)->orWhere(id_value) without grouping)
 *   2. Course and intake using separate whereHas so a student could match
 *      course A on one registration and intake B on another
 */
class StudentViewFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actor = User::forceCreate([
            'name'          => 'Program Admin',
            'email'         => 'admin@nebula.lk',
            'password'      => Hash::make('password'),
            'user_role'     => 'Program Admin L1',
            'status'        => '1',
            'user_location' => 'Welisara',
        ]);
    }

    // -------------------------------------------------------------------------
    // Helper to create a Student + optionally a CourseRegistration
    // -------------------------------------------------------------------------

    private function makeStudent(string $idValue, array $studentAttrs = []): Student
    {
        return Student::forceCreate(array_merge([
            'name_with_initials' => 'Test Student',
            'full_name'          => 'Test Student Full',
            'id_type'            => 'NIC',
            'id_value'           => $idValue,
            'gender'             => 'Male',
            'email'              => $idValue . '@test.lk',
            'academic_status'    => 'active',
            'institute_location' => 'Welisara',
        ], $studentAttrs));
    }

    private function makeRegistration(int $studentId, int $courseId, int $intakeId, string $location = 'Welisara'): CourseRegistration
    {
        return CourseRegistration::forceCreate([
            'student_id'        => $studentId,
            'course_id'         => $courseId,
            'intake_id'         => $intakeId,
            'status'            => 'Registered',
            'approval_status'   => 'Approved by manager',
            'location'          => $location,
            'registration_date' => now()->toDateString(),
        ]);
    }

    private function route(array $params = []): string
    {
        return '/student-view?' . http_build_query($params);
    }

    // =========================================================================
    // Fix 1: student_id search must not bypass course/intake constraints
    // =========================================================================

    public function test_student_id_search_without_course_filter_returns_student(): void
    {
        $student = $this->makeStudent('199012345678');

        $response = $this->actingAs($this->actor)
            ->getJson($this->route(['student_id' => '199012345678']));

        $response->assertOk();
        $ids = collect($response->json('students'))->pluck('student_id');
        $this->assertContains($student->student_id, $ids);
    }

    public function test_student_id_search_with_mismatched_course_does_not_return_student(): void
    {
        // student is registered to course 1 only
        $student = $this->makeStudent('199099999999');
        $this->makeRegistration($student->student_id, 1, 1);

        // search for that student but filter by course 2 — should return nothing
        $response = $this->actingAs($this->actor)
            ->getJson($this->route([
                'student_id' => '199099999999',
                'course_id'  => 2,
            ]));

        $response->assertOk();
        $ids = collect($response->json('students'))->pluck('student_id');
        $this->assertNotContains(
            $student->student_id,
            $ids,
            'A student_id match must not bypass the course_id constraint'
        );
    }

    // =========================================================================
    // Fix 2: course AND intake must be satisfied by the SAME registration row
    // =========================================================================

    public function test_student_with_course_a_intake_b_on_separate_registrations_is_excluded(): void
    {
        // Student has two registrations:
        //   reg 1: course=10, intake=100
        //   reg 2: course=20, intake=200
        // Filter: course=10 AND intake=200 — no single row satisfies both.
        $student = $this->makeStudent('200011112222');
        $this->makeRegistration($student->student_id, 10, 100);
        $this->makeRegistration($student->student_id, 20, 200);

        $response = $this->actingAs($this->actor)
            ->getJson($this->route([
                'course_id'  => 10,
                'intake_id'  => 200,
            ]));

        $response->assertOk();
        $ids = collect($response->json('students'))->pluck('student_id');
        $this->assertNotContains(
            $student->student_id,
            $ids,
            'Student must not appear when no single registration satisfies both course and intake'
        );
    }

    public function test_student_with_matching_course_and_intake_on_same_registration_is_included(): void
    {
        $student = $this->makeStudent('200099998888');
        $this->makeRegistration($student->student_id, 10, 100);

        $response = $this->actingAs($this->actor)
            ->getJson($this->route([
                'course_id'  => 10,
                'intake_id'  => 100,
            ]));

        $response->assertOk();
        $ids = collect($response->json('students'))->pluck('student_id');
        $this->assertContains(
            $student->student_id,
            $ids,
            'Student should appear when a single registration satisfies both course and intake'
        );
    }
}
