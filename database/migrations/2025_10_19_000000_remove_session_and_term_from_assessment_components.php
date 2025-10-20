<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop Foreign Keys if they exist
        Schema::table('assessment_components', function (Blueprint $table) {
            // Drop foreign keys using array notation (Laravel will figure out the constraint name)
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

        // Drop unique indexes if they exist
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

        // Drop columns session_id and term_id if they exist
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

        // Recreate unique indexes if they were dropped
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

        Schema::enableForeignKeyConstraints();
    }
};