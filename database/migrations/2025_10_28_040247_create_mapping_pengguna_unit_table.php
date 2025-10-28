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
        Schema::create('mapping_pengguna_unit', function (Blueprint $table) {
            $table->id();
            // Menyambung ke tabel 'users'
            $table->unsignedInteger('user_id');
            // Menyambung ke tabel 'departemen'
            // Kita samakan tipenya dengan tabel lama & departemen Bng
            $table->string('id_ruang', 20)->nullable();
            // Kolom 'level' (operator, pj, dll) kita pindah ke sini
            $table->string('level', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_pengguna_unit');
    }
};
