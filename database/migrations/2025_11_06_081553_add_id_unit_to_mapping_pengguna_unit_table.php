<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mapping_pengguna_unit', function (Blueprint $table) {
            // $table->unsignedInteger('id_unit')->nullable()->after('id_ruang'); // <-- KOMENTARI BARIS INI
            $table->foreign('id_unit')->references('id')->on('unit')->onDelete('cascade');
        });
    }

    // Tambahkan juga fungsi down() untuk rollback
    public function down(): void
    {
        Schema::table('mapping_pengguna_unit', function (Blueprint $table) {
            $table->dropForeign(['id_unit']);
            $table->dropColumn('id_unit');
        });
    }
};
