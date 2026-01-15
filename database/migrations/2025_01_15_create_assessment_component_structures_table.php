<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_component_structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id');
            $table->uuid('assessment_component_id');
            $table->uuid('class_id')->nullable(); // NULL = applies to all classes
            $table->uuid('term_id')->nullable(); // NULL = applies to all terms
            $table->decimal('max_score', 8, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('assessment_component_id')->references('id')->on('assessment_components')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');

            // Composite unique constraint - one structure per component/class/term combination
            $table->unique(['assessment_component_id', 'class_id', 'term_id'], 'unique_component_class_term');

            // Index for faster queries
            $table->index(['assessment_component_id', 'class_id', 'term_id'], 'idx_acs_component_class_term');
            $table->index(['school_id'], 'idx_acs_school');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_component_structures');
    }
};
