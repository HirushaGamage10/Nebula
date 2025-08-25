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
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->decimal('calculated_late_fee', 10, 2)->default(0); // system-generated
            $table->decimal('approved_late_fee', 10, 2)->nullable();   // final payable after waiver
            $table->unsignedBigInteger('approved_by')->nullable();     // user_id of approver
            $table->text('approval_note')->nullable();                 // reason/note
        });
    }

    public function down()
    {
        Schema::table('payment_installments', function (Blueprint $table) {
            $table->dropColumn(['calculated_late_fee', 'approved_late_fee', 'approved_by', 'approval_note']);
        });
    }

};
