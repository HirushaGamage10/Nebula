<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration safely changes the marketing_survey column to TEXT so
     * long joined survey values won't be truncated and cause SQL warnings.
     */
    public function up()
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'marketing_survey')) {
            Schema::table('students', function (Blueprint $table) {
                // Use raw statement for compatibility across MySQL versions
                $driver = config('database.default');
                // Only change column type when supported
                try {
                    // For MySQL/Postgres the following will work when using doctrine/dbal, but
                    // we avoid depending on it and instead run a raw ALTER if possible.
                    if (config('database.connections.' . $driver . '.driver') === 'mysql') {
                        DB::statement('ALTER TABLE `students` MODIFY `marketing_survey` TEXT NULL');
                    } else {
                        // Fallback: attempt to use schema builder change (may require doctrine/dbal)
                        $table->text('marketing_survey')->nullable()->change();
                    }
                } catch (\Exception $e) {
                    // Log and continue; this migration is best-effort on dev environments.
                    \Log::warning('Could not modify students.marketing_survey column: ' . $e->getMessage());
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // We won't automatically revert to VARCHAR because original length is not known.
        // Leave as-is.
    }
};
