<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_change_logs')) {
            return;
        }

        Schema::table('course_change_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('course_change_logs', 'old_course_id')) {
                $table->unsignedBigInteger('old_course_id')->nullable()->after('old_intake_id');
            }
            if (!Schema::hasColumn('course_change_logs', 'new_course_id')) {
                $table->unsignedBigInteger('new_course_id')->nullable()->after('new_intake_id');
            }
            if (!Schema::hasColumn('course_change_logs', 'old_payment_plan_id')) {
                $table->unsignedBigInteger('old_payment_plan_id')->nullable()->after('new_course_id');
            }
            if (!Schema::hasColumn('course_change_logs', 'total_paid_amount')) {
                $table->decimal('total_paid_amount', 12, 2)->default(0)->after('old_payment_plan_id');
            }
            if (!Schema::hasColumn('course_change_logs', 'changed_by_name')) {
                $table->string('changed_by_name')->nullable()->after('changed_by');
            }
            if (!Schema::hasColumn('course_change_logs', 'changed_at')) {
                $table->timestamp('changed_at')->nullable()->after('changed_by_name');
            }
            if (!Schema::hasColumn('course_change_logs', 'remarks')) {
                $table->text('remarks')->nullable()->after('changed_at');
            }
        });
    }

    public function down(): void
    {
        // No-op repair migration.
    }
};
