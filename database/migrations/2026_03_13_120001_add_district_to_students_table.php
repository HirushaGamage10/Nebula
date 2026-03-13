<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('district', [
                'Ampara',
                'Anuradhapura',
                'Badulla',
                'Batticaloa',
                'Colombo',
                'Galle',
                'Gampaha',
                'Hambantota',
                'Jaffna',
                'Kalutara',
                'Kandy',
                'Kegalle',
                'Kilinochchi',
                'Kurunegala',
                'Mannar',
                'Matale',
                'Matara',
                'Monaragala',
                'Mullaitivu',
                'Nuwara Eliya',
                'Polonnaruwa',
                'Puttalam',
                'Ratnapura',
                'Trincomalee',
                'Vavuniya',
            ])->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('district');
        });
    }
};
