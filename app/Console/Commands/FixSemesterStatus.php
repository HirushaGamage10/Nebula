<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Semester;

class FixSemesterStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'semester:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate semester statuses based on current date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting semester status recalculation...');
        
        $semesters = Semester::all();
        $updated = 0;
        
        foreach ($semesters as $semester) {
            $oldStatus = $semester->status;
            
            // Calculate correct status based on current date
            $today = now()->toDateString();
            if ($semester->start_date > $today) {
                $newStatus = 'upcoming';
            } elseif ($semester->start_date <= $today && $semester->end_date >= $today) {
                $newStatus = 'active';
            } else {
                $newStatus = 'completed';
            }
            
            // Update if status changed
            if ($oldStatus !== $newStatus) {
                $semester->update(['status' => $newStatus]);
                $this->line("Semester ID {$semester->id} ({$semester->name}): {$oldStatus} → {$newStatus}");
                $updated++;
            }
        }
        
        $this->info("Completed! Updated {$updated} semester(s).");
        
        return 0;
    }
}
