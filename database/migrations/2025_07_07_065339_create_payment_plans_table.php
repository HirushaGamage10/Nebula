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
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('course_type');
            $table->unsignedBigInteger('course_id');
            $table->decimal('registration_fee', 12, 2);
            $table->decimal('local_fee', 12, 2);
            $table->decimal('international_fee', 12, 2);
            $table->string('international_currency', 10)->nullable();
            $table->decimal('sscl_tax', 5, 2);
            $table->decimal('bank_charges', 12, 2)->nullable();
            $table->boolean('apply_discount')->default(false);
            $table->decimal('discount', 5, 2)->nullable();
            $table->boolean('installment_plan')->default(false);
            $table->json('installments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_plans');
    }
};
