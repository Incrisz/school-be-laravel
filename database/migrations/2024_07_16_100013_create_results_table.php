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
        Schema::create('results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('subject_id');
            $table->uuid('term_id');
            $table->uuid('session_id');
            $table->decimal('total_score', 5, 2);
            $table->integer('position_in_subject')->nullable();
            $table->decimal('lowest_in_class', 5, 2)->nullable();
            $table->decimal('highest_in_class', 5, 2)->nullable();
            $table->decimal('class_average', 5, 2)->nullable();
            $table->uuid('grade_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('restrict');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('restrict');
            $table->foreign('grade_id')->references('id')->on('grade_ranges')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
};
