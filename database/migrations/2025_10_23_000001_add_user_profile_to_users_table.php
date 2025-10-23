<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'user_profile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_profile')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'user_profile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_profile');
            });
        }
    }
};
