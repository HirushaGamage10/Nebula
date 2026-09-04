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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE semester_registrations 
                MODIFY status ENUM('registered', 'pending', 'cancelled', 'terminated') 
                DEFAULT 'registered'");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE semester_registrations 
                MODIFY status ENUM('registered', 'pending', 'cancelled') 
                DEFAULT 'registered'");
        }
    }
};
