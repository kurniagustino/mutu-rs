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
        Schema::create('hospital_survey_indicator_variable', function (Blueprint $table) {
            $table->increments('variable_id');
            $table->integer('variable_indicator_id');
            $table->string('variable_name', 255);
            $table->enum('variable_type', ['N', 'D']); // N untuk Numerator, D untuk Denumerator
            $table->text('variable_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_survey_indicator_variable');
    }
};