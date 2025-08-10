<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('semester_registrations', function (Blueprint $table) {
            // What the user is trying to do when approval is required
            $table->enum('desired_status', ['registered','terminated'])
                  ->nullable()
                  ->after('status');

            // Special approval workflow state
            $table->enum('approval_status', ['none','pending','approved','rejected'])
                  ->default('none')
                  ->after('desired_status');

            // Why they’re asking (required in UI)
            $table->text('approval_reason')->nullable()->after('approval_status');

            // Optional attachment (PDF, images, etc.)
            $table->string('approval_file_path')->nullable()->after('approval_reason');

            // DGM audit trail
            $table->text('approval_dgm_comment')->nullable()->after('approval_file_path');
            $table->timestamp('approval_requested_at')->nullable()->after('approval_dgm_comment');
            $table->timestamp('approval_decided_at')->nullable()->after('approval_requested_at');
            $table->unsignedBigInteger('approval_decided_by')->nullable()->after('approval_decided_at');

            // Helpful index
            $table->index(['student_id','intake_id','semester_id'], 'semreg_student_intake_sem_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('semester_registrations', function (Blueprint $table) {
            $table->dropIndex('semreg_student_intake_sem_idx');
            $table->dropColumn([
                'desired_status',
                'approval_status',
                'approval_reason',
                'approval_file_path',
                'approval_dgm_comment',
                'approval_requested_at',
                'approval_decided_at',
                'approval_decided_by',
            ]);
        });
    }
};
