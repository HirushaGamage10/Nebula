<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE team_roles MODIFY COLUMN role ENUM('Leader', 'Developer', 'BA', 'QA', 'DevOps')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE team_roles MODIFY COLUMN role ENUM('Leader', 'Developer', 'BA', 'QA')");
    }
};
