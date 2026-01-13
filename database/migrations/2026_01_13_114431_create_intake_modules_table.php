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
        Schema::create('intake_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('intake_id');
            $table->unsignedBigInteger('module_id');
            $table->timestamps();

            $table->foreign('intake_id')->references('intake_id')->on('intakes')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
            
            $table->unique(['intake_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intake_modules');
    }
};
