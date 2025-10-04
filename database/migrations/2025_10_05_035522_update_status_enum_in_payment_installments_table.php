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
    DB::statement("ALTER TABLE payment_installments MODIFY COLUMN status ENUM('pending','paid','overdue','archived') DEFAULT 'pending'");
}

public function down()
{
    DB::statement("ALTER TABLE payment_installments MODIFY COLUMN status ENUM('pending','paid','overdue') DEFAULT 'pending'");
}

};
