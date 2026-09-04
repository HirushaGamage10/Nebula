<?php

namespace Tests\Feature;

use App\Http\Controllers\CourseRegistraionController;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseLocationDropdownContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_course_lookup_returns_success_false_payload(): void
    {
        $controller = new CourseRegistraionController();

        $response = $controller->getCoursesByLocation('NoSuchLocation');
        $payload = json_decode($response->getContent(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('success', $payload);
        $this->assertFalse($payload['success']);
        $this->assertSame([], $payload['courses']);
    }

    public function test_valid_course_lookup_returns_success_true_payload(): void
    {
        Course::create([
            'course_name'         => 'Diploma in Software Engineering',
            'course_type'         => 'degree',
            'location'            => 'Moratuwa',
            'no_of_semesters'     => 8,
            'duration'            => '4 years',
            'min_credits'         => 120,
            'course_medium'       => 'English',
            'entry_qualification' => 'A/L or equivalent',
            'conducted_by'        => 0,
        ]);

        $controller = new CourseRegistraionController();

        $response = $controller->getCoursesByLocation('Moratuwa');
        $payload = json_decode($response->getContent(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($payload['success']);
        $this->assertNotEmpty($payload['courses']);
    }
}
