<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DGMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the initial DGM user
        User::create([
            'name' => 'Deputy General Manager',
            'email' => 'dgm@nebula.com',
            'employee_id' => 'DGM001',
            'password' => Hash::make('dgm123456'),
            'user_role' => 'DGM',
            'status' => '1', // Active
            'user_location' => 'Nebula Institute of Technology – Welisara',
        ]);

        $this->command->info('DGM user created successfully!');
        $this->command->info('Email: dgm@nebula.com');
        $this->command->info('Password: dgm123456');
    }
} 