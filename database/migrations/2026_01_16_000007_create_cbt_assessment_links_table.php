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
        Schema::create('cbt_assessment_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_component_id');
            $table->uuid('cbt_exam_id');
            $table->uuid('class_id')->nullable();
            $table->uuid('term_id')->nullable();
            $table->uuid('session_id')->nullable();
            $table->uuid('subject_id')->nullable();
            $table->boolean('auto_sync')->default(false);
            $table->string('score_mapping_type', 20)->default('direct');
            $table->decimal('max_score_override', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('assessment_component_id')
                ->references('id')
                ->on('assessment_components')
                ->onDelete('cascade');
            $table->foreign('cbt_exam_id')
                ->references('id')
                ->on('quizzes')
                ->onDelete('cascade');
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->nullOnDelete();
            $table->foreign('term_id')
                ->references('id')
                ->on('terms')
                ->nullOnDelete();
            $table->foreign('session_id')
                ->references('id')
                ->on('sessions')
                ->nullOnDelete();
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->nullOnDelete();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('assessment_component_id');
            $table->index('cbt_exam_id');
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_assessment_links');
    }
};
