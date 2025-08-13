<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@nebula.com'],
            [
                'name' => 'Admin User',
                'employee_id' => 'EMP001',
                'user_role' => 'DGM',
                'status' => '1',
                'user_location' => 'Nebula Institute of Technology – Welisara',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create level 01 program admin if it doesn't exist
        User::firstOrCreate(
            ['email' => 'pa1@nebula.com'],
            [
                'name' => 'Level 01 Program Admin',
                'employee_id' => 'EMP002',
                'user_role' => 'Program Administrator (level 01)',
                'status' => '1',
                'user_location' => 'Nebula Institute of Technology – Welisara',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin User: admin@nebula.com / password123');
        $this->command->info('Program Admin: pa1@nebula.com / password123');
    }
}
