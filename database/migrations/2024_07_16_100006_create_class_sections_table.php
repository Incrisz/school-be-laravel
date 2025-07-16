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
        Schema::create('class_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_arm_id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('class_arm_id')->references('id')->on('class_arms')->onDelete('cascade');
            $table->unique(['class_arm_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_sections');
    }
};
