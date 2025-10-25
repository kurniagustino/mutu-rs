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
        Schema::create('hospital_survey_indicator_result', function (Blueprint $table) {
            $table->increments('result_id');
            $table->integer('result_indicator_id');
            $table->string('result_department_id', 20)->nullable();
            $table->string('result_numerator_value', 10)->nullable();
            $table->string('result_denumerator_value', 10)->nullable();
            $table->date('result_period');
            $table->dateTime('result_post_date');
            $table->char('result_record_status', 1)->default('A');
            $table->string('last_edited_by', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_survey_indicator_result');
    }
};