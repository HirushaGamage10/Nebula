<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_roles', function (Blueprint $table) {
            $table->enum('role', ['Leader', 'Developer', 'BA', 'QA', 'DevOps'])
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('team_roles', function (Blueprint $table) {
            $table->enum('role', ['leader', 'developer', 'BA', 'QA'])
                  ->change();
        });
    }
};
