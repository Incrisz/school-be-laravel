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
        Schema::create('class_teachers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('staff_id');
            $table->uuid('class_section_id');
            $table->uuid('session_id');
            $table->uuid('term_id');
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_teachers');
    }
};
