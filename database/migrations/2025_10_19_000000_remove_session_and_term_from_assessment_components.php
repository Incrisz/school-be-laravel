<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // First, find and drop foreign keys from OTHER tables that reference this unique index
        $this->dropReferencingForeignKeys();

        // Drop foreign keys on the assessment_components table itself
        Schema::table('assessment_components', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_components', 'session_id')) {
                try {
                    $table->dropForeign(['session_id']);
                } catch (\Exception $e) {
                    // ignore if constraint doesn't exist
                }
            }

            if (Schema::hasColumn('assessment_components', 'term_id')) {
                try {
                    $table->dropForeign(['term_id']);
                } catch (\Exception $e) {
                    // ignore if constraint doesn't exist
                }
            }
        });

        // Now drop unique indexes
        Schema::table('assessment_components', function (Blueprint $table) {
            try {
                $table->dropUnique('assessment_components_unique_per_context_no_subject');
            } catch (\Exception $e) {
                // ignore if index doesn't exist
            }

            try {
                $table->dropUnique('assessment_components_unique_per_context');
            } catch (\Exception $e) {
                // ignore if index doesn't exist
            }
        });

        // Finally, drop the columns
        Schema::table('assessment_components', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('assessment_components', 'session_id')) {
                $columnsToDrop[] = 'session_id';
            }

            if (Schema::hasColumn('assessment_components', 'term_id')) {
                $columnsToDrop[] = 'term_id';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Add columns session_id and term_id back
        Schema::table('assessment_components', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_components', 'session_id')) {
                $table->uuid('session_id')->nullable()->after('school_id');
            }

            if (!Schema::hasColumn('assessment_components', 'term_id')) {
                $table->uuid('term_id')->nullable()->after('session_id');
            }
        });

        // Add foreign keys for session_id and term_id
        Schema::table('assessment_components', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_components', 'session_id')) {
                try {
                    $table->foreign('session_id')
                        ->references('id')
                        ->on('sessions')
                        ->nullOnDelete();
                } catch (\Exception $e) {
                    // ignore if foreign key already exists
                }
            }

            if (Schema::hasColumn('assessment_components', 'term_id')) {
                try {
                    $table->foreign('term_id')
                        ->references('id')
                        ->on('terms')
                        ->nullOnDelete();
                } catch (\Exception $e) {
                    // ignore if foreign key already exists
                }
            }
        });

        // Recreate unique indexes
        Schema::table('assessment_components', function (Blueprint $table) {
            try {
                $table->unique(
                    ['school_id', 'session_id', 'term_id', 'name'],
                    'assessment_components_unique_per_context_no_subject'
                );
            } catch (\Exception $e) {
                // ignore if index already exists
            }
        });

        // Note: We cannot recreate foreign keys from other tables here
        // as we don't know which tables they came from. This should be
        // handled in separate migrations for those tables.

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Find and drop foreign keys from other tables that reference assessment_components
     */
    private function dropReferencingForeignKeys(): void
    {
        $schema = Schema::getConnection()->getDatabaseName();
        
        // Find all foreign keys that reference assessment_components table
        $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = ?
              AND REFERENCED_TABLE_NAME = 'assessment_components'
              AND TABLE_NAME != 'assessment_components'
        ", [$schema]);

        // Drop each foreign key found
        foreach ($foreignKeys as $fk) {
            try {
                Schema::table($fk->TABLE_NAME, function (Blueprint $table) use ($fk) {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                });
            } catch (\Exception $e) {
                // Continue even if we can't drop a foreign key
            }
        }
    }
};