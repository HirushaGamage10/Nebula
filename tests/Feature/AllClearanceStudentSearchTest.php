<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for AllClearanceController student search functionality.
 *
 * Verifies that searching by student_id and id_value (NIC/ID)
 * executes without unknown column 'nic' SQL errors.
 */
class AllClearanceStudentSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $actor;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actor = User::forceCreate([
            'name'          => 'Program Admin',
            'email'         => 'clearance_admin@nebula.lk',
            'password'      => Hash::make('password'),
            'user_role'     => 'Program Administrator (level 01)',
            'status'        => '1',
            'user_location' => 'Welisara',
        ]);

        $this->student = Student::forceCreate([
            'name_with_initials' => 'K. L. Silva',
            'full_name'          => 'Kasun Lahiru Silva',
            'id_type'            => 'NIC',
            'id_value'           => '199812345678',
            'gender'             => 'Male',
            'email'              => 'kasun@test.lk',
            'institute_location' => 'Welisara',
            'academic_status'    => 'active',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public function test_all_clearance_search_by_numeric_student_id(): void
    {
        $response = $this->actingAs($this->actor)
            ->get('/all-clearance?student_id=' . $this->student->student_id);

        $response->assertOk();
        $response->assertViewHas('student', function ($foundStudent) {
            return $foundStudent !== null && $foundStudent->student_id === $this->student->student_id;
        });
    }

    public function test_all_clearance_search_by_nic_id_value(): void
    {
        $response = $this->actingAs($this->actor)
            ->get('/all-clearance?student_id=199812345678');

        $response->assertOk();
        $response->assertViewHas('student', function ($foundStudent) {
            return $foundStudent !== null && $foundStudent->id_value === '199812345678';
        });
    }

    public function test_all_clearance_search_with_nonexistent_identifier(): void
    {
        $response = $this->actingAs($this->actor)
            ->get('/all-clearance?student_id=999999999999');

        $response->assertOk();
        $response->assertViewHas('student', null);
    }
}