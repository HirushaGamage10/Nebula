<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Intake;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for UhIndexController save/update logic.
 *
 * Verifies that saving a UH index number scopes the update strictly to
 * the selected course and intake, preventing clobbering of different
 * course registrations for the same student (57+ students have multi-registrations).
 */
class UhIndexScopedUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $actor;
    private int $courseAId;
    private int $courseBId;
    private int $intakeAId;
    private int $intakeBId;
    private int $studentId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actor = User::forceCreate([
            'name'          => 'Program Admin',
            'email'         => 'admin_uh@nebula.lk',
            'password'      => Hash::make('password'),
            'user_role'     => 'Program Administrator (level 01)',
            'status'        => '1',
            'user_location' => 'Welisara',
        ]);

        $this->courseAId = DB::table('courses')->insertGetId([
            'course_name' => 'BSc Computing',
            'course_type' => 'degree',
            'location'    => 'Welisara',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->courseBId = DB::table('courses')->insertGetId([
            'course_name' => 'BSc Networking',
            'course_type' => 'degree',
            'location'    => 'Welisara',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->intakeAId = DB::table('intakes')->insertGetId([
            'course_id'   => $this->courseAId,
            'course_name' => 'BSc Computing',
            'batch'       => '2024-01',
            'location'    => 'Welisara',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->intakeBId = DB::table('intakes')->insertGetId([
            'course_id'   => $this->courseBId,
            'course_name' => 'BSc Networking',
            'batch'       => '2024-02',
            'location'    => 'Welisara',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->studentId = DB::table('students')->insertGetId([
            'name_with_initials' => 'A. B. Perera',
            'full_name'          => 'Ananda Bandara Perera',
            'id_type'            => 'NIC',
            'id_value'           => '199501010101',
            'gender'             => 'Male',
            'email'              => 'ananda@test.lk',
            'institute_location' => 'Welisara',
            'academic_status'    => 'active',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public function test_saving_uh_index_updates_only_matching_course_and_intake_registration(): void
    {
        // Student has TWO registrations:
        // Registration 1: Course A, Intake A (UH: UH-COMP-001)
        $regAId = DB::table('course_registration')->insertGetId([
            'student_id'        => $this->studentId,
            'course_id'         => $this->courseAId,
            'intake_id'         => $this->intakeAId,
            'status'            => 'Registered',
            'approval_status'   => 'Approved by manager',
            'location'          => 'Welisara',
            'registration_date' => now()->toDateString(),
            'uh_index_number'   => 'OLD-UH-A',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Registration 2: Course B, Intake B (UH: OLD-UH-B)
        $regBId = DB::table('course_registration')->insertGetId([
            'student_id'        => $this->studentId,
            'course_id'         => $this->courseBId,
            'intake_id'         => $this->intakeBId,
            'status'            => 'Registered',
            'approval_status'   => 'Approved by manager',
            'location'          => 'Welisara',
            'registration_date' => now()->toDateString(),
            'uh_index_number'   => 'OLD-UH-B',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Update UH index for Course A, Intake A ONLY
        $response = $this->actingAs($this->actor)->postJson('/uh-index/save', [
            'course_id' => $this->courseAId,
            'intake_id' => $this->intakeAId,
            'students'  => [
                [
                    'student_id'      => (string) $this->studentId,
                    'uh_index_number' => 'NEW-UH-A',
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'updated_count' => 1]);

        // Registration A MUST be updated to NEW-UH-A
        $this->assertSame(
            'NEW-UH-A',
            DB::table('course_registration')->where('id', $regAId)->value('uh_index_number'),
            'Registration for Course A / Intake A should be updated'
        );

        // Registration B MUST remain OLD-UH-B (NOT overwritten)
        $this->assertSame(
            'OLD-UH-B',
            DB::table('course_registration')->where('id', $regBId)->value('uh_index_number'),
            'Registration for Course B / Intake B must NOT be overwritten'
        );
    }

    public function test_save_fails_gracefully_when_no_registration_matches_course_and_intake(): void
    {
        // Student only registered in Course A
        DB::table('course_registration')->insert([
            'student_id'        => $this->studentId,
            'course_id'         => $this->courseAId,
            'intake_id'         => $this->intakeAId,
            'status'            => 'Registered',
            'approval_status'   => 'Approved by manager',
            'location'          => 'Welisara',
            'registration_date' => now()->toDateString(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Try saving for Course B where student is NOT registered
        $response = $this->actingAs($this->actor)->postJson('/uh-index/save', [
            'course_id' => $this->courseBId,
            'intake_id' => $this->intakeBId,
            'students'  => [
                [
                    'student_id'      => (string) $this->studentId,
                    'uh_index_number' => 'NEW-UH',
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }
}