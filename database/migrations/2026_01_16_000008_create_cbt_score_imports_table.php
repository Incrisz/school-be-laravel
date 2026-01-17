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
        Schema::create('cbt_score_imports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cbt_assessment_link_id');
            $table->uuid('student_id');
            $table->decimal('cbt_raw_score', 8, 2);
            $table->decimal('cbt_max_score', 8, 2);
            $table->decimal('converted_score', 8, 2)->nullable();
            $table->string('status', 20)->default('pending');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('cbt_assessment_link_id')
                ->references('id')
                ->on('cbt_assessment_links')
                ->onDelete('cascade');
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique(['cbt_assessment_link_id', 'student_id'], 'cbt_import_unique');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_score_imports');
    }
};
