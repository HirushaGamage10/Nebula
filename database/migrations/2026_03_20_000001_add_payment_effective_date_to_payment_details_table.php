<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            // Stores the actual date the student made the payment, which may differ
            // from the system update date when admins record payments retrospectively.
            // Late fee calculations use this date, not created_at / updated_at.
            $table->date('payment_effective_date')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('payment_effective_date');
        });
    }
};
