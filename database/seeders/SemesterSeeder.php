<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Module;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get existing courses and intakes
        $courses = Course::all();
        $intakes = Intake::all();
        
        if ($courses->isEmpty() || $intakes->isEmpty()) {
            $this->command->info('No courses or intakes found. Please run course and intake seeders first.');
            return;
        }

        // Create active semesters for testing elective module registration
        foreach ($courses as $course) {
            foreach ($intakes->take(2) as $intake) { // Create for first 2 intakes
                // Create an active semester (current date falls between start and end)
                Semester::create([
                    'name' => 'Semester 3',
                    'course_id' => $course->course_id,
                    'intake_id' => $intake->intake_id,
                    'start_date' => now()->subDays(30), // Started 30 days ago
                    'end_date' => now()->addDays(60),   // Ends in 60 days
                    'status' => 'active',
                ]);

                // Create an upcoming semester
                Semester::create([
                    'name' => 'Semester 4',
                    'course_id' => $course->course_id,
                    'intake_id' => $intake->intake_id,
                    'start_date' => now()->addDays(70), // Starts in 70 days
                    'end_date' => now()->addDays(160),  // Ends in 160 days
                    'status' => 'upcoming',
                ]);
            }
        }

        // Create some elective modules if they don't exist
        $electiveModules = [
            [
                'module_code' => 'ELEC001',
                'module_name' => 'Advanced Programming',
                'module_type' => 'elective',
                'module_cordinator' => 'Dr. Smith',
                'credits' => 3,
            ],
            [
                'module_code' => 'ELEC002',
                'module_name' => 'Database Management',
                'module_type' => 'elective',
                'module_cordinator' => 'Prof. Johnson',
                'credits' => 4,
            ],
            [
                'module_code' => 'ELEC003',
                'module_name' => 'Web Development',
                'module_type' => 'elective',
                'module_cordinator' => 'Ms. Davis',
                'credits' => 3,
            ],
            [
                'module_code' => 'ELEC004',
                'module_name' => 'Mobile App Development',
                'module_type' => 'elective',
                'module_cordinator' => 'Mr. Wilson',
                'credits' => 4,
            ],
        ];

        foreach ($electiveModules as $moduleData) {
            $module = Module::firstOrCreate(
                ['module_code' => $moduleData['module_code']],
                $moduleData
            );

            // Associate elective modules with courses (as elective modules)
            foreach ($courses as $course) {
                $module->courses()->syncWithoutDetaching([
                    $course->course_id => [
                        'is_core' => false, // This makes it an elective module
                        'semester' => 3,    // Available in semester 3
                    ]
                ]);
            }
        }

        $this->command->info('Semester and elective module test data created successfully!');
    }
} 