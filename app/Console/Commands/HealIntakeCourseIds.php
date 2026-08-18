<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HealIntakeCourseIds extends Command
{
    protected $signature   = 'intakes:heal-course-ids {--dry-run : Show what would be updated without making changes}';
    protected $description = 'Backfill course_id on intake rows that have course_id = NULL by matching on course_name + location.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Find intakes missing course_id that can be matched to a course
        $rows = DB::select("
            SELECT i.intake_id, i.course_name, i.location, c.course_id
            FROM intakes i
            JOIN courses c ON c.course_name = i.course_name AND c.location = i.location
            WHERE i.course_id IS NULL
        ");

        if (empty($rows)) {
            $this->info('No intakes with NULL course_id found — nothing to heal.');
            return self::SUCCESS;
        }

        $this->info(count($rows) . ' intake row(s) will be updated.');

        if ($dryRun) {
            $this->table(['intake_id', 'course_name', 'location', 'course_id (will assign)'],
                array_map(fn($r) => [(array) $r], $rows)[0] ?? []
            );
            foreach ($rows as $row) {
                $this->line("  intake_id={$row->intake_id}  course_name=\"{$row->course_name}\"  → course_id={$row->course_id}");
            }
            $this->warn('Dry-run mode — no changes written.');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($rows as $row) {
            DB::table('intakes')
                ->where('intake_id', $row->intake_id)
                ->whereNull('course_id')
                ->update(array_merge(
                    ['course_id' => $row->course_id],
                    \App\Support\UserTrackingData::forUpdate()
                ));
            $updated++;
        }

        $this->info("Done. {$updated} intake row(s) updated.");
        return self::SUCCESS;
    }
}
