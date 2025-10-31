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
            $table->string('indicator_name', 255)->nullable();

            // --- 3 FIELD BARU DARI PDF (SESUAI OBROLAN) ---
            $table->string('dimensi_mutu', 255)->nullable();
            $table->text('tujuan')->nullable();
            $table->string('satuan_pengukuran', 50)->nullable(); // Pengganti type_persen

            // --- PROFIL INDIKATOR LAINNYA ---
            $table->text('indicator_definition')->nullable();
            $table->text('indicator_criteria_inclusive')->nullable();
            $table->text('indicator_criteria_exclusive')->nullable();
            $table->string('indicator_source_of_data', 255)->nullable();

            // --- PENGATURAN INDIKATOR ---
            $table->string('indicator_type', 50)->nullable(); // (Proses, Outcome)
            $table->string('indicator_monitoring_area', 200)->nullable(); // (Unit)
            $table->string('indicator_frequency', 50)->nullable(); // (Bulanan)
            $table->string('indicator_target', 10)->nullable()->default('0');
            $table->integer('urutan')->nullable();

            // --- RELASI (SESUAI OBROLAN) ---
            $table->integer('indicator_category_id')->nullable();
            $table->unsignedInteger('penanggung_jawab_id')->nullable(); // Opsional, relasi ke user

            // --- FIELD YANG ANDA MINTA TETAP ADA ---
            $table->char('indicator_record_status', 1)->nullable()->default('A');

            $table->text('files')->nullable();
            $table->timestamps(); // Standar Laravel

            // --- FIELD YANG DIHAPUS ---
            // indicator_element, indicator_value_standard, indicator_iscomplete
            // status_kunci, tampil_survey, type_persen, imut_must_valid
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
