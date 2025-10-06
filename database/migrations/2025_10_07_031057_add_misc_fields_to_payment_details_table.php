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
    Schema::table('payment_details', function (Blueprint $table) {
        $table->string('misc_category')->nullable()->after('course_registration_id');
        $table->string('misc_reference')->nullable()->after('misc_category');
        $table->text('description')->nullable()->after('misc_reference');
    });
}

public function down()
{
    Schema::table('payment_details', function (Blueprint $table) {
        $table->dropColumn(['misc_category', 'misc_reference', 'description']);
    });
}

};
