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
        Schema::table('hospital_survey_indicator', function (Blueprint $table) {
            // Menambahkan kolom indicator_imut_type setelah indicator_category_id
            $table->string('indicator_imut_type', 20)
                  ->nullable()
                  ->after('indicator_category_id')
                  ->comment('Jenis IMUT: INM, IKP, dll');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospital_survey_indicator', function (Blueprint $table) {
            $table->dropColumn('indicator_imut_type');
        });
    }
};
