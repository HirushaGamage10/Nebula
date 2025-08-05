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
        Schema::table('payment_details', function (Blueprint $table) {
            $table->date('payment_date')->nullable()->after('due_date');
        });
    }

    public function down()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('payment_date');
        });
    }

};
