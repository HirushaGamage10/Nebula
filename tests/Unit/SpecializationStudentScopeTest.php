<?php

namespace Tests\Unit;

use App\Support\SemesterModuleSpecializationHelper;
use App\Support\SpecializationStudentScope;
use Tests\TestCase;

class SpecializationStudentScopeTest extends TestCase
{
    public function test_module_scope_matches_specialization_filtering(): void
    {
        $this->assertTrue(SemesterModuleSpecializationHelper::matchesSelection(null, null, 'Common'));
        $this->assertTrue(SemesterModuleSpecializationHelper::matchesSelection(json_encode(['Software Engineering', 'Networking']), null, 'Common'));
        $this->assertTrue(SemesterModuleSpecializationHelper::matchesSelection(json_encode(['Software Engineering', 'Networking']), null, 'Software Engineering'));
        $this->assertFalse(SemesterModuleSpecializationHelper::matchesSelection(json_encode(['Software Engineering']), null, 'Networking'));
        $this->assertFalse(SemesterModuleSpecializationHelper::matchesSelection(null, null, 'Software Engineering'));
    }

    public function test_common_selection_expands_to_module_specializations(): void
    {
        $moduleScope = json_encode(['Software Engineering', 'Networking']);

        $this->assertSame(
            ['Software Engineering', 'Networking'],
            SpecializationStudentScope::resolveSelectionSpecializations('Common', $moduleScope, null)
        );

        $this->assertSame(
            ['Software Engineering'],
            SpecializationStudentScope::resolveSelectionSpecializations('Software Engineering', $moduleScope, null)
        );
    }

    public function test_common_selection_is_case_insensitive_and_accepts_course_scope(): void
    {
        $this->assertSame(
            ['Software Engineering', 'Networking'],
            SpecializationStudentScope::resolveSelectionSpecializations('common', null, null, ['Software Engineering', 'Networking'])
        );

        $this->assertSame(
            ['Software Engineering', 'Networking'],
            SpecializationStudentScope::resolveSelectionSpecializations('Common', json_encode(['Software Engineering', 'Networking']), null)
        );
    }
}
