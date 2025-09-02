<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->json('partial_payments')->nullable()->after('remarks');
            $table->decimal('remaining_amount', 12, 2)->default(0)->after('total_fee');
        });
    }

    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn(['partial_payments', 'remaining_amount']);
        });
    }
};
