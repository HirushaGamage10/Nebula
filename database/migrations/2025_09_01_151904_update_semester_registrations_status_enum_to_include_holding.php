<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE semester_registrations 
                MODIFY status ENUM('registered', 'pending', 'cancelled', 'terminated', 'holding') 
                DEFAULT 'registered'");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE semester_registrations 
                MODIFY status ENUM('registered', 'pending', 'cancelled', 'terminated') 
                DEFAULT 'registered'");
        }
    }
};