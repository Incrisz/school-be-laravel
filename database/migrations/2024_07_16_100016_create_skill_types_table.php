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
        Schema::create('skill_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('skill_category_id');
            $table->uuid('school_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('skill_category_id')->references('id')->on('skill_categories')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_types');
    }
};
