<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Helpers\RoleHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_helper_has_permission()
    {
        // Test that DGM has permission to access special approval
        $this->assertTrue(RoleHelper::hasPermission('DGM', 'special.approval'));
        
        // Test that Librarian does not have permission to access student registration
        $this->assertFalse(RoleHelper::hasPermission('Librarian', 'student.registration'));
        
        // Test that Librarian has permission to access library clearance
        $this->assertTrue(RoleHelper::hasPermission('Librarian', 'library.clearance'));
    }

    public function test_role_helper_can_access_student_management()
    {
        // Test that Student Counselor can access student management
        $this->assertTrue(RoleHelper::canAccessStudentManagement('Student Counselor'));
        
        // Test that Librarian cannot access student management
        $this->assertFalse(RoleHelper::canAccessStudentManagement('Librarian'));
    }

    public function test_role_helper_can_access_clearance()
    {
        // Test that Librarian can access clearance
        $this->assertTrue(RoleHelper::canAccessClearance('Librarian'));
        
        // Test that Hostel Manager can access clearance
        $this->assertTrue(RoleHelper::canAccessClearance('Hostel Manager'));
        
        // Test that Student Counselor cannot access clearance
        $this->assertFalse(RoleHelper::canAccessClearance('Student Counselor'));
    }

    public function test_developer_role_has_full_access()
    {
        // Test that Developer has access to all major permissions
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'dashboard'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'create.user'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'user.management'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'module.management'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'course.management'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'student.registration'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'course.registration'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'attendance'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'special.approval'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'library.clearance'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'hostel.clearance.form.management'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'project.clearance.management'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'payment.plan'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'payment.clearance'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'reporting.dashboard'));
        $this->assertTrue(RoleHelper::hasPermission('Developer', 'data.export.import'));

    }

    public function test_developer_role_can_access_all_management_features()
    {
        // Test that Developer can access all management features
        $this->assertTrue(RoleHelper::canAccessStudentManagement('Developer'));
        $this->assertTrue(RoleHelper::canAccessClearance('Developer'));
        $this->assertTrue(RoleHelper::canAccessAcademicManagement('Developer'));
        $this->assertTrue(RoleHelper::canAccessAttendance('Developer'));
    }
} 