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
        Schema::table('intakes', function (Blueprint $table) {
            // Check if franchise_payment column exists before modifying it
            if (Schema::hasColumn('intakes', 'franchise_payment')) {
                $table->string('franchise_payment')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Check if franchise_payment column exists before modifying it
            if (Schema::hasColumn('intakes', 'franchise_payment')) {
                $table->string('franchise_payment')->nullable(false)->change();
            }
        });
    }
};
