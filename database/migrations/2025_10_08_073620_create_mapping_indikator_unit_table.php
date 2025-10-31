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
        Schema::create('mapping_indikator_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_unit')->nullable();
            $table->unsignedInteger('id_indikator')->nullable();
            $table->integer('status')->nullable();
            $table->timestamp('created')->nullable();
            $table->string('tahun', 4)->nullable();
            $table->string('statuspmkp', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_indikator_unit');
    }
};
