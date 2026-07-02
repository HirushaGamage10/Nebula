<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semester_module', function (Blueprint $table) {
            if (!Schema::hasColumn('semester_module', 'specializations')) {
                $table->json('specializations')->nullable()->after('specialization');
            }
        });

        if (!Schema::hasColumn('semester_module', 'specializations')) {
            return;
        }

        DB::table('semester_module')->orderBy('semester_id')->orderBy('module_id')->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                if ($row->specializations !== null) {
                    continue;
                }

                $specializations = null;
                $specialization = $row->specialization ?? null;

                if ($specialization !== null && $specialization !== '' && $specialization !== 'General') {
                    $specializations = json_encode([$specialization]);
                }

                DB::table('semester_module')
                    ->where('semester_id', $row->semester_id)
                    ->where('module_id', $row->module_id)
                    ->update(['specializations' => $specializations]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('semester_module', function (Blueprint $table) {
            if (Schema::hasColumn('semester_module', 'specializations')) {
                $table->dropColumn('specializations');
            }
        });
    }
};
