<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateDGMUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:dgm {--email=dgm@nebula.com} {--password=dgm123456}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the initial DGM user for the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        // Check if DGM user already exists
        if (User::where('user_role', 'DGM')->exists()) {
            $this->error('A DGM user already exists in the system!');
            return 1;
        }

        // Create the DGM user
        User::create([
            'name' => 'Deputy General Manager',
            'email' => $email,
            'employee_id' => 'DGM001',
            'password' => Hash::make($password),
            'user_role' => 'DGM',
            'status' => '1', // Active
            'user_location' => 'Nebula Institute of Technology – Welisara',
        ]);

        $this->info('DGM user created successfully!');
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info('You can now login to the system and create other users.');

        return 0;
    }
} 