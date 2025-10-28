<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospital_survey_indicator_result', function (Blueprint $table) {
            // ✅ Tambah created_at dan updated_at
            $table->timestamps(); // Adds created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::table('hospital_survey_indicator_result', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
