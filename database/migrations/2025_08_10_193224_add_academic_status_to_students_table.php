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
        Schema::table('students', function (Blueprint $table) {
            // Add academic_status WITHOUT touching existing 'status' (marital)
            $table->enum('academic_status', ['active','terminated','suspended','graduated'])
                  ->default('active')
                  ->after('status'); // place next to marital status for clarity

            // (Optional) quick audit fields
            $table->text('academic_status_reason')->nullable()->after('academic_status');
            $table->string('academic_status_document')->nullable()->after('academic_status_reason');
            $table->timestamp('academic_status_changed_at')->nullable()->after('academic_status_document');

            $table->index('academic_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['academic_status']);
            $table->dropColumn([
                'academic_status',
                'academic_status_reason',
                'academic_status_document',
                'academic_status_changed_at',
            ]);
        });
    }
};
