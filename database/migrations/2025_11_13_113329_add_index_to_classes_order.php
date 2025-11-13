<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if index already exists
        $indexExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = 'classes'
            AND index_name = 'classes_school_order_index'
        ");

        if ($indexExists[0]->count == 0) {
            Schema::table('classes', function (Blueprint $table) {
                // Add composite index for school_id and order for efficient sorting
                $table->index(['school_id', 'order'], 'classes_school_order_index');
            });
        }

        // Update existing classes with proper order values
        DB::statement("SET @school_row_number = 0");
        DB::statement("SET @current_school_id = NULL");

        DB::statement("
            UPDATE classes c
            INNER JOIN (
                SELECT
                    id,
                    school_id,
                    name,
                    @school_row_number := IF(@current_school_id = school_id, @school_row_number + 1, 1) AS new_order,
                    @current_school_id := school_id
                FROM classes
                ORDER BY school_id,
                    CASE
                        WHEN name LIKE 'Nursery%' THEN 1
                        WHEN name LIKE 'Primary%' THEN 2
                        WHEN name LIKE 'JSS%' THEN 3
                        WHEN name LIKE 'SS%' THEN 4
                        ELSE 5
                    END,
                    CAST(SUBSTRING_INDEX(name, ' ', -1) AS UNSIGNED)
            ) AS ordered_classes ON c.id = ordered_classes.id
            SET c.order = ordered_classes.new_order
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex('classes_school_order_index');
        });

        // Reset order values to 0
        DB::table('classes')->update(['order' => 0]);
    }
};
