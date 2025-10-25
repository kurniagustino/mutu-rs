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
        Schema::create('hospital_survey_indicator', function (Blueprint $table) {
            $table->increments('indicator_id');
            $table->text('indicator_definition')->nullable();
            $table->text('indicator_criteria_inclusive')->nullable();
            $table->text('indicator_criteria_exclusive')->nullable();
            $table->text('indicator_element')->nullable();
            $table->text('indicator_element_2021')->nullable();
            $table->string('indicator_source_of_data', 100)->nullable();
            $table->string('indicator_type', 50)->nullable();
            $table->integer('indicator_value_standard')->nullable();
            $table->string('indicator_monitoring_area', 200)->nullable();

            // --- INI PERBAIKAN UTAMANYA ---
            $table->string('indicator_frequency', 50)->nullable(); // Diperbesar dari char(1) jadi string(50)

            $table->string('indicator_target', 5)->nullable()->default('0');
            $table->integer('indicator_category_id')->nullable()->default(2);
            $table->integer('indicator_iscomplete')->nullable()->default(0);
            $table->char('indicator_record_status', 1)->nullable()->default('A');
            $table->char('status_kunci', 1)->nullable()->default('0');
            $table->char('tampil_survey', 1)->nullable()->default('0');
            $table->enum('kategori', ['NASIONAL', 'RS', 'UNIT'])->nullable();
            $table->integer('urutan')->nullable();
            $table->enum('type_persen', ['PERSEN', 'Menit'])->nullable()->default('PERSEN');
            $table->enum('imut_must_valid', ['Y', 'N'])->nullable()->default('N');
            $table->text('files')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_survey_indicator');
    }
};
