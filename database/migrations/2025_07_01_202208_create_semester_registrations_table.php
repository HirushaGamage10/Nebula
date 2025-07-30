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
        // Schema::create('semester_registrations', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        // Table creation commented out because the table already exists in the database and this migration was causing errors.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semester_registrations');
    }
};
