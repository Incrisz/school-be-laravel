<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_ranges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('grading_scale_id');
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('grade_label', 50);
            $table->string('description')->nullable();
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->timestamps();

            $table->foreign('grading_scale_id')->references('id')->on('grading_scales')->onDelete('cascade');
            $table->unique(['grading_scale_id', 'grade_label']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grade_ranges');
    }
};
