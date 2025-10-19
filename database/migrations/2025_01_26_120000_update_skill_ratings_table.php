<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('skill_ratings', 'remarks')) {
            Schema::table('skill_ratings', function (Blueprint $table) {
                $table->dropColumn('remarks');
            });
        }

        Schema::table('skill_ratings', function (Blueprint $table) {
            $table->unique(
                ['student_id', 'term_id', 'session_id', 'skill_type_id'],
                'skill_ratings_unique_student_term_skill'
            );
        });
    }

    public function down(): void
    {
        Schema::table('skill_ratings', function (Blueprint $table) {
            $table->dropUnique('skill_ratings_unique_student_term_skill');
        });
    }
};
