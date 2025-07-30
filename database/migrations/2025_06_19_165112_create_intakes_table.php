<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('intakes', function (Blueprint $table) {
            $table->id('intake_id');
            $table->enum('location', ['Welisara', 'Moratuwa', 'Peradeniya']);
            $table->string('course_name'); // You could normalize this to FK if courses are reused exactly
            $table->string('batch');
            $table->integer('batch_size');
            $table->enum('intake_mode', ['Physical', 'Online', 'Hybrid']);
            $table->enum('intake_type', ['Fulltime', 'Parttime']);
            $table->string('registration_fee');
            $table->string('franchise_payment');
            $table->string('course_fee');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('intakes');
    }
};
