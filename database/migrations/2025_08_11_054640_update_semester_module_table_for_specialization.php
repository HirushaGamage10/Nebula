<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if table exists and has the expected structure
        if (!Schema::hasTable('semester_module')) {
            return;
        }

        Schema::table('semester_module', function (Blueprint $table) {
            // Check if foreign keys exist before dropping them
            $foreignKeys = $this->getForeignKeys('semester_module');
            
            if (in_array('semester_module_semester_id_foreign', $foreignKeys)) {
                $table->dropForeign(['semester_id']);
            }
            if (in_array('semester_module_module_id_foreign', $foreignKeys)) {
                $table->dropForeign(['module_id']);
            }
        });

        Schema::table('semester_module', function (Blueprint $table) {
            // Add specialization column if not exists
            if (!Schema::hasColumn('semester_module', 'specialization')) {
                $table->string('specialization')->nullable()->after('module_id');
            }
        });

        // Update existing records to have a default specialization value
        DB::table('semester_module')->whereNull('specialization')->update(['specialization' => 'General']);

        Schema::table('semester_module', function (Blueprint $table) {
            // Check if primary key exists before dropping it
            $primaryKey = $this->getPrimaryKey('semester_module');
            if ($primaryKey && $primaryKey === 'PRIMARY') {
                $table->dropPrimary(['semester_id', 'module_id']);
            }

            // Add new composite primary key
            $table->primary(['semester_id', 'module_id', 'specialization']);
        });

        Schema::table('semester_module', function (Blueprint $table) {
            // Re-add foreign keys
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('semester_module')) {
            return;
        }

        Schema::table('semester_module', function (Blueprint $table) {
            $primaryKey = $this->getPrimaryKey('semester_module');
            if ($primaryKey && $primaryKey === 'PRIMARY') {
                $table->dropPrimary(['semester_id', 'module_id', 'specialization']);
            }
            if (Schema::hasColumn('semester_module', 'specialization')) {
                $table->dropColumn('specialization');
            }
            $table->primary(['semester_id', 'module_id']);
        });
    }

    private function getForeignKeys($tableName)
    {
        $foreignKeys = [];
        try {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$tableName]);
            
            foreach ($constraints as $constraint) {
                $foreignKeys[] = $constraint->CONSTRAINT_NAME;
            }
        } catch (\Exception $e) {
            // If query fails, return empty array
        }
        
        return $foreignKeys;
    }

    private function getPrimaryKey($tableName)
    {
        try {
            $result = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND CONSTRAINT_TYPE = 'PRIMARY KEY'
            ", [$tableName]);
            
            return $result[0]->CONSTRAINT_NAME ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
};