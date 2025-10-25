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
        Schema::create('indikator_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('indikator_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('indikator_id')
                  ->references('indicator_id')
                  ->on('hospital_survey_indicator')
                  ->onDelete('cascade');
            
            $table->foreign('status_id')
                  ->references('id')
                  ->on('status_kategori')
                  ->onDelete('cascade');

            // Unique constraint untuk mencegah duplikat
            $table->unique(['indikator_id', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_status');
    }
};
