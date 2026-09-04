<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample degree courses for Welisara
        Course::create([
            'location' => 'Welisara',
            'course_name' => 'Bachelor of Science in Computer Science',
            'course_type' => 'degree',
            'no_of_semesters' => 4,
            'duration' => '2 years',
            'min_credits' => 120,
            'entry_qualification' => 'A/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'English',
        ]);

        Course::create([
            'location' => 'Welisara',
            'course_name' => 'Bachelor of Business Administration',
            'course_type' => 'degree',
            'no_of_semesters' => 4,
            'duration' => '2 years',
            'min_credits' => 120,
            'entry_qualification' => 'A/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'English',
        ]);

        // Sample diploma courses for Welisara
        Course::create([
            'location' => 'Welisara',
            'course_name' => 'Diploma in Information Technology',
            'course_type' => 'diploma',
            'no_of_semesters' => 2,
            'duration' => '1 year',
            'min_credits' => 60,
            'entry_qualification' => 'O/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'English',
        ]);

        // Sample courses for Moratuwa
        Course::create([
            'location' => 'Moratuwa',
            'course_name' => 'Bachelor of Science in Software Engineering',
            'course_type' => 'degree',
            'no_of_semesters' => 4,
            'duration' => '2 years',
            'min_credits' => 120,
            'entry_qualification' => 'A/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'English',
        ]);

        Course::create([
            'location' => 'Moratuwa',
            'course_name' => 'Diploma in Web Development',
            'course_type' => 'diploma',
            'no_of_semesters' => 2,
            'duration' => '1 year',
            'min_credits' => 60,
            'entry_qualification' => 'O/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'English',
        ]);

        // Sample courses for Peradeniya
        Course::create([
            'location' => 'Peradeniya',
            'course_name' => 'Bachelor of Engineering in Information Technology',
            'course_type' => 'degree',
            'no_of_semesters' => 4,
            'duration' => '2 years',
            'min_credits' => 120,
            'entry_qualification' => 'A/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'Sinhala',
        ]);

        Course::create([
            'location' => 'Peradeniya',
            'course_name' => 'Diploma in Digital Marketing',
            'course_type' => 'diploma',
            'no_of_semesters' => 2,
            'duration' => '1 year',
            'min_credits' => 60,
            'entry_qualification' => 'O/L Pass',
            'conducted_by' => 1,
            'course_medium' => 'Sinhala',
        ]);
    }
}
