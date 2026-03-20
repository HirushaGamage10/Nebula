<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Developer user
        User::create([
            'name' => 'Developer User',
            'email' => 'developer@nebula.com',
            'employee_id' => 'DEV001',
            'password' => Hash::make('Dev@123'),
            'user_role' => 'Developer',
            'user_roles' => json_encode(['Developer', 'DGM']),
            'status' => '1',
            'user_location' => 'Welisara',
        ]);

        // Create a Program Administrator
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@nebula.com',
            'employee_id' => 'ADM001',
            'password' => Hash::make('Admin@123'),
            'user_role' => 'Program Administrator (level 01)',
            'user_roles' => json_encode(['Program Administrator (level 01)']),
            'status' => '1',
            'user_location' => 'Welisara',
        ]);

        // Create a DGM user
        User::create([
            'name' => 'DGM User',
            'email' => 'dgm@nebula.com',
            'employee_id' => 'DGM001',
            'password' => Hash::make('DGM@123'),
            'user_role' => 'DGM',
            'user_roles' => json_encode(['DGM']),
            'status' => '1',
            'user_location' => 'Welisara',
        ]);
    }
}
