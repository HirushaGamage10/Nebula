<?php

namespace Tests\Unit;

use App\Models\CourseRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for CourseRegistration::scopeEligible()
 *
 * Covers the central eligibility rule that replaced scattered hardcoded closures
 * across SpecializationRegistrationController, RepeatStudentsController,
 * SemesterRegistrationController, and AllClearanceController.
 */
class CourseRegistrationEligibleScopeTest extends TestCase
{
    use RefreshDatabase;

    private function makeReg(array $attrs): CourseRegistration
    {
        return CourseRegistration::forceCreate(array_merge([
            'student_id'        => 1,
            'course_id'         => 1,
            'intake_id'         => 1,
            'status'            => 'Pending',
            'approval_status'   => 'Pending',
            'location'          => 'Welisara',
            'registration_date' => now()->toDateString(),
        ], $attrs));
    }

    public function test_status_registered_is_eligible(): void
    {
        $reg = $this->makeReg(['status' => 'Registered', 'approval_status' => 'Pending']);
        $result = CourseRegistration::eligible()->pluck('id');
        $this->assertContains($reg->id, $result);
    }

    public function test_approval_status_approved_by_manager_is_eligible(): void
    {
        $reg = $this->makeReg(['status' => 'Pending', 'approval_status' => 'Approved by manager']);
        $result = CourseRegistration::eligible()->pluck('id');
        $this->assertContains($reg->id, $result);
    }

    public function test_registered_status_with_manager_approval_is_eligible(): void
    {
        $reg = $this->makeReg(['status' => 'Registered', 'approval_status' => 'Approved by manager']);
        $result = CourseRegistration::eligible()->pluck('id');
        $this->assertContains($reg->id, $result);
    }

    public function test_pending_only_row_is_not_eligible(): void
    {
        $reg = $this->makeReg(['status' => 'Pending', 'approval_status' => 'Pending']);
        $result = CourseRegistration::eligible()->pluck('id');
        $this->assertNotContains($reg->id, $result);
    }

    public function test_stale_approved_by_dgm_value_is_not_eligible(): void
    {
        $reg = $this->makeReg(['status' => 'Pending', 'approval_status' => 'Approved by DGM']);
        $result = CourseRegistration::eligible()->pluck('id');
        $this->assertNotContains($reg->id, $result);
    }

    public function test_eligible_scope_returns_only_qualifying_rows(): void
    {
        $eligible1 = $this->makeReg(['status' => 'Registered',   'approval_status' => 'Pending']);
        $eligible2 = $this->makeReg(['status' => 'Pending',      'approval_status' => 'Approved by manager']);
        $excluded1 = $this->makeReg(['status' => 'Pending',      'approval_status' => 'Pending']);
        $excluded2 = $this->makeReg(['status' => 'Pending',      'approval_status' => 'Approved by DGM']);

        $ids = CourseRegistration::eligible()->pluck('id');

        $this->assertContains($eligible1->id, $ids, 'status=Registered should be eligible');
        $this->assertContains($eligible2->id, $ids, 'approval_status=Approved by manager should be eligible');
        $this->assertNotContains($excluded1->id, $ids, 'Pending-only row should not be eligible');
        $this->assertNotContains($excluded2->id, $ids, 'Approved by DGM row should not be eligible');
        $this->assertCount(2, $ids);
    }
}
