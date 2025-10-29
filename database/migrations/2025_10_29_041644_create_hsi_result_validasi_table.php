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
        Schema::create('hsi_result_validasi', function (Blueprint $table) {
            $table->id('result_id');
            $table->unsignedInteger('result_indicator_id');
            $table->string('result_department_id', 20)->nullable();
            $table->string('result_numerator_value', 10)->nullable();
            $table->string('result_denumerator_value', 10)->nullable();
            $table->string('rn_rekap_valid', 10)->nullable()->comment('Numerator Rekap Validasi');
            $table->string('rd_rekap_valid', 10)->nullable()->comment('Denominator Rekap Validasi');
            $table->string('validasi_pmkp', 10)->nullable()->comment('Status Validasi PMKP');
            $table->string('result_period', 50)->default('')->comment('Format: YYYY-MM');
            $table->dateTime('result_post_date');
            $table->char('result_record_status', 1)->default('A')->comment('A=Active, D=Deleted');
            $table->string('last_edited_by', 5);

            // Indexes
            $table->index('result_indicator_id');
            $table->index('result_period');
            $table->index('result_record_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hsi_result_validasi');
    }
};
