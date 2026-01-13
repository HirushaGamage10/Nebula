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
        Schema::table('modules', function (Blueprint $table) {
            $table->string('module_category')->default('degree')->after('module_name');
            $table->string('module_type')->nullable()->change();
            $table->string('credits')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('module_category');
            $table->string('module_type')->nullable(false)->change();
            $table->string('credits')->nullable(false)->change();
        });
    }
};
