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
    public function up(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->string('foreign_currency_code')->nullable(); // e.g., USD
            $table->decimal('foreign_currency_amount', 15, 2)->nullable(); // original amount
        });
    }

    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn(['foreign_currency_code', 'foreign_currency_amount']);
        });
    }

};
