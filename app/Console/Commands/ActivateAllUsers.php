<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ActivateAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:activate-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status=1 (active) for all users in the users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = User::query()->update(['status' => '1']);
        $this->info("Activated $count users.");
        return 0;
    }
} 