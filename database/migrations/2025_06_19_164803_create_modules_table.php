<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('modules', function (Blueprint $table) {
            $table->id('module_id');
            $table->string('module_code')->unique();
            $table->string('module_name');
            $table->enum('module_type', ['core', 'elective', 'special_unit_compulsory']);
            $table->string('module_cordinator')->nullable();
            $table->integer('credits');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('modules');
    }
};
