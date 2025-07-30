<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@nebula.com',
            'employee_id' => 'EMP001',
            'user_role' => 'DGM',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Level 01 Program Admin',
            'email' => 'pa1@nebula.com',
            'employee_id' => 'EMP002',
            'user_role' => 'Program Administrator (level 01)',
            'status' => '1',
            'user_location' => 'Nebula Institute of Technology – Welisara',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
