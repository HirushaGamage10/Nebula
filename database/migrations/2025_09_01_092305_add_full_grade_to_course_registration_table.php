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
        Schema::table('course_registration', function (Blueprint $table) {
            $table->string('full_grade')->nullable()->after('remarks');
        });
    }
    public function down()
    {
        Schema::table('course_registration', function (Blueprint $table) {
            $table->dropColumn('full_grade');
        });
    }
};
